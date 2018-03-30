<?php

namespace frontend\controllers;

use backend\models\Category;
use backend\models\Goods;

class IndexController extends \yii\web\Controller
{
    /*
     * 主页显示
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**分类搜索
     * @param $id
     * @return string
     */
    public function actionList($id)
    {
        //找到当前分类对象
        $cate=Category::findOne($id);

        //根据对象找到所有子孙分类
        $cates=Category::find()->where(['tree'=>$cate->tree])->andWhere(['>=','lft',$cate->lft])->andWhere(['<=','rgt',$cate->rgt])->asArray()->all();

//        var_dump($cates);
        //二维转一维 获取分类对象的id
        $cates_Id = array_column($cates,'id');
//        var_dump($cates_Id);exit;

        //获得该分类下的所有商品
        $goods = Goods::find()->where(['in','cate_id',$cates_Id])->andWhere(['status'=>1])->orderBy('sort')->all();
//        var_dump($goods);exit;


        return $this->render('list',compact('goods'));
    }


}
