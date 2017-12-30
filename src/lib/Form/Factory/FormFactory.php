<?php

namespace Edgar\EzUIBookmark\Form\Factory;

use Edgar\EzUIBookmark\Form\Type\BookmarkAddType;
use Edgar\EzUIBookmark\Form\Type\BookmarkDeleteType;
use Edgar\EzUIBookmarkBundle\Entity\EdgarEzBookmark;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormFactory
{
    /** @var FormFactoryInterface $formFactory */
    protected $formFactory;

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function addBookmark(
        EdgarEzBookmark $data,
        ?string $name = null
    ): ?FormInterface {
        $name = $name ?: 'bookmark-add';

        return $this->formFactory->createNamed(
            $name,
            BookmarkAddType::class,
            $data,
            [
                'method' => Request::METHOD_POST,
                'csrf_protection' => true,
            ]
        );
    }

    public function deleteBookmark(
        EdgarEzBookmark $data,
        ?string $name = null
    ): ?FormInterface {
        $name = $name ?: 'bookmark-delete';

        return $this->formFactory->createNamed(
            $name,
            BookmarkDeleteType::class,
            $data,
            [
                'method' => Request::METHOD_POST,
                'csrf_protection' => true,
            ]
        );
    }
}
