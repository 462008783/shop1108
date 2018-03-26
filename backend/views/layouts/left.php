<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?=Yii::$app->user->identity->username??"";?></p>

                <a href="#"><i class="fa fa-circle text-success"></i> <?=Yii::$app->user->isGuest?"离线":"在线"?></a>
            </div>
        </div>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->

        <?php
        $callback = function($menu){
            $data = json_decode($menu['data'], true);
            $items = $menu['children'];
            $return = [
                'label' => $menu['name'],
                'url' => [$menu['route']],
            ];
            //处理我们的配置
            if ($data) {
                //visible
                isset($data['visible']) && $return['visible'] = $data['visible'];
                //icon
                isset($data['icon']) && $data['icon'] && $return['icon'] = $data['icon'];
                //other attribute e.g. class...
                $return['options'] = $data;
            }
            //没配置图标的显示默认图标
            (!isset($return['icon']) || !$return['icon']) && $return['icon'] = 'fa fa-circle-o';
            $items && $return['items'] = $items;
            return $return;
        };?>
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => \mdm\admin\components\MenuHelper::getAssignedMenu(Yii::$app->user->id, null, $callback),
//                    ['label' => 'Menu Yii2', 'options' => ['class' => 'header']],
//                    [
//                        'label' => '商品管理',
//                        'icon' => 'shopping-basket',
//                        'url' => '#',
//                        'items' => [
//                            ['label' => '商品列表', 'icon' => 'bars', 'url' => ['/goods/index'],],
//                            ['label' => '添加商品', 'icon' => 'plus-square', 'url' => ['/goods/add'],],
////                            [
////                                'label' => 'Level One',
////                                'icon' => 'circle-o',
////                                'url' => '#',
////                                'items' => [
////                                    ['label' => 'Level Two', 'icon' => 'circle-o', 'url' => '#',],
////                                    [
////                                        'label' => 'Level Two',
////                                        'icon' => 'circle-o',
////                                        'url' => '#',
////                                        'items' => [
////                                            ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
////                                            ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
////                                        ],
////                                    ],
////                                ],
////                            ],
//                        ],
//                    ],
//                    [
//                        'label' => '商品分类',
//                        'icon' => 'suitcase',
//                        'url' => '#',
//                        'items' =>
//                            [
//                            ['label' => '分类列表', 'icon' => 'bars', 'url' => ['/category/index'],],
//                            ['label' => '添加分类', 'icon' => 'plus-square', 'url' => ['/category/add'],],
//                        ],
//                    ],
//                    [
//                        'label' => '品牌管理',
//                        'icon' => 'shopping-bag',
//                        'url' => '#',
//                        'items' => [
//                            ['label' => '品牌列表', 'icon' => 'bars', 'url' => ['/brand/index'],],
//                            ['label' => '添加品牌', 'icon' => 'plus-square', 'url' => ['/brand/add'],],
//                        ],
//                    ],
//                    [
//                        'label' => '文章管理',
//                        'icon' => 'sticky-note-o',
//                        'url' => '#',
//                        'items' => [
//                            ['label' => '文章列表', 'icon' => 'bars', 'url' => ['/article/index'],],
//                            ['label' => '添加文章', 'icon' => 'plus-square', 'url' => ['/article/add'],],
//                            ],
//                    ],
//                    [
//                        'label' => '文章分类',
//                        'icon' => 'book',
//                        'url' => '#',
//                        'items' => [
//                            ['label' => '分类列表', 'icon' => 'bars', 'url' => ['/article-category/index'],],
//                            ['label' => '添加分类', 'icon' => 'plus-square', 'url' => ['/article-category/add'],],
//                        ],
//                    ],[
//                        'label' => '管理员分类',
//                        'icon' => 'address-card',
//                        'url' => '#',
//                        'items' => [
//                            ['label' => '管理员列表', 'icon' => 'bars', 'url' => ['/admin/show'],],
//                            ['label' => '添加管理员', 'icon' => 'plus-square', 'url' => ['/admin/add'],],
//                        ],
//                    ],
////                    ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii']],
////                    ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug']],
////                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
//                ],
            ]
        ) ?>

    </section>

</aside>
