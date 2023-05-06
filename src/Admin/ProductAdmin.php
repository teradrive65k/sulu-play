<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Product;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;

class ProductAdmin extends Admin
{
    final public const LIST_VIEW = 'app.product.list';
    final public const ADD_FORM_VIEW = 'app.product.add_form';
    final public const ADD_FORM_DETAILS_VIEW = 'app.product.add_form.details';
    final public const EDIT_FORM_VIEW = 'app.product.edit_form';
    final public const EDIT_FORM_DETAILS_VIEW = 'app.product.edit_form.details';

    public function __construct(private readonly ViewBuilderFactoryInterface $viewBuilderFactory, private readonly SecurityCheckerInterface $securityChecker)
    {
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        if ($this->securityChecker->hasPermission(Product::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $rootNavigationItem = new NavigationItem('Sales');
            $rootNavigationItem->setIcon('fa-dropbox');
            $rootNavigationItem->setPosition(25);

            $navigationItem = new NavigationItem('Products');
            $navigationItem->setView(static::LIST_VIEW);

            $rootNavigationItem->addChild($navigationItem);
            $navigationItemCollection->add($rootNavigationItem);
        }
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $formToolbarActions = [];
        $listToolbarActions = [];

        if ($this->securityChecker->hasPermission(Product::SECURITY_CONTEXT, PermissionTypes::ADD)) {
            $listToolbarActions[] = new ToolbarAction('sulu_admin.add');
        }

        if ($this->securityChecker->hasPermission(Product::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $formToolbarActions[] = new ToolbarAction('sulu_admin.save');
        }

        if ($this->securityChecker->hasPermission(Product::SECURITY_CONTEXT, PermissionTypes::DELETE)) {
            $formToolbarActions[] = new ToolbarAction('sulu_admin.delete');
            $listToolbarActions[] = new ToolbarAction('sulu_admin.delete');
        }

        if ($this->securityChecker->hasPermission(Product::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            $listToolbarActions[] = new ToolbarAction('sulu_admin.export');
        }

        if ($this->securityChecker->hasPermission(Product::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $viewCollection->add(
                $this->viewBuilderFactory->createListViewBuilder(static::LIST_VIEW, '/products')
                    ->setResourceKey(Product::RESOURCE_KEY)
                    ->setListKey(Product::LIST_KEY)
                    ->setTitle('Products')
                    ->addListAdapters(['table'])
                    ->setAddView(static::ADD_FORM_VIEW)
                    ->setEditView(static::EDIT_FORM_VIEW)
                    ->addToolbarActions($listToolbarActions),
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createResourceTabViewBuilder(static::ADD_FORM_VIEW, '/product/add')
                    ->setResourceKey(Product::RESOURCE_KEY)
                    ->setBackView(static::LIST_VIEW),
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createFormViewBuilder(static::ADD_FORM_DETAILS_VIEW, '/details')
                    ->setResourceKey(Product::RESOURCE_KEY)
                    ->setFormKey(Product::FORM_KEY)
                    ->setTabTitle('sulu_admin.details')
                    ->setEditView(static::EDIT_FORM_VIEW)
                    ->addToolbarActions($formToolbarActions)
                    ->setParent(static::ADD_FORM_VIEW),
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createResourceTabViewBuilder(static::EDIT_FORM_VIEW, '/product/:id')
                    ->setResourceKey(Product::RESOURCE_KEY)
                    ->setBackView(static::LIST_VIEW),
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createFormViewBuilder(static::EDIT_FORM_DETAILS_VIEW, '/details')
                    ->setResourceKey(Product::RESOURCE_KEY)
                    ->setFormKey(Product::FORM_KEY)
                    ->setTabTitle('sulu_admin.details')
                    ->addToolbarActions($formToolbarActions)
                    ->setParent(static::EDIT_FORM_VIEW),
            );
        }
    }

    public function getSecurityContexts(): array
    {
        return [
            self::SULU_ADMIN_SECURITY_SYSTEM => [
                'Product' => [
                    Product::SECURITY_CONTEXT => [
                        PermissionTypes::VIEW,
                        PermissionTypes::ADD,
                        PermissionTypes::EDIT,
                        PermissionTypes::DELETE,
                    ],
                ],
            ],
        ];
    }
}
