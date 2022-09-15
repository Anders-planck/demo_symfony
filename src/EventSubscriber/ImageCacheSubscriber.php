<?php

namespace App\EventSubscriber;

use App\Entity\Post;
use App\Events\PostEvent;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageCacheSubscriber implements EventSubscriberInterface
{
    private CacheManager $cacheManager;
    private UploaderHelper $uploaderHelper;

    public function __construct(CacheManager $cacheManager, UploaderHelper $uploaderHelper)
    {
        $this->cacheManager = $cacheManager;
        $this->uploaderHelper = $uploaderHelper;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
                'post.pre_remove' => 'preRemove',
                'post.pre_update' => 'preUpdate'

        ];
    }

    public function preRemove(PostEvent $args)
    {
        dump("remove Event");
        $entity = $args->getPost();
        $this->cacheManager->remove($this->uploaderHelper->asset($entity,'imageFile'));
    }

    public function preUpdate(PostEvent $args)
    {
        dump('update event');
        $entity = $args->getPost();
        if($entity->getImageFile() instanceof  UploadedFile){
            dump('hi');
            $this->cacheManager->remove($this->uploaderHelper->asset($entity,'imageFile'));
        }
    }


}
