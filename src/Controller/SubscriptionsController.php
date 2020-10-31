<?php

namespace App\Controller;

use App\Event\EnqueueEvent;
use App\Repository\MORepository;
use App\Validator\SubscribeRequestValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class SubscriptionsController extends AbstractController
{
    // TODO: Move to parameters & split controller by actions
    private const CACHE_STATS_SECS = 5;
    private const TIME_SPAN = 10000;
    private const LAST_INSERTIONS_MINS = 15;


    public function create(
        Request $request,
        SubscribeRequestValidator $validator,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $data['text'] = filter_var($data['text'], FILTER_SANITIZE_STRING);
        if (false === $validator->validate($data, $error)) {
            throw new BadRequestHttpException('Invalid parameter: ' . $error);
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['node'] = $_SERVER['SERVER_ADDR'] ?? $_SERVER['SERVER_NAME'] ?? exec('hostname -i');
        $eventDispatcher->dispatch(new EnqueueEvent($data));

        return $this->json(['status' => 'ok']);
    }


    public function statistics(Request $request, CacheInterface $cache, MORepository $moRepository)
    {
        $key = 'stats';
        $stats = $cache->get(
            $key,
            function (ItemInterface $item) use ($moRepository) {
                $item->expiresAfter(self::CACHE_STATS_SECS);

                return [
                    'last_mo_inserts_mins' => $moRepository->countLastInsertions(self::LAST_INSERTIONS_MINS),
                    'time_span_last_10k'   => $moRepository->getTimeSpan(self::TIME_SPAN)
                ];
            }
        );

        return $this->json($stats);
    }
}