<?php
class productController extends pec_pageController {
	
	public function createAction()
	{
		$model = new productModel();
		if(isset($_POST['productForm']))
		{
			$model->assign($_POST['productForm']);
			if($model->validate() && $model->save())
			{
				$this->redirect('/product/adminview/'.$model->id);
			}
		}
		$this->setViewVar('model',$model);
		$this->render();
	}
	
	public function editAction()
	{
		$id = (int)$this->params['id'];
		$model = new productModel($id);
		if(isset($_POST['productForm']))
		{
			$model->assign($_POST['productForm']);
			if($model->validate() && $model->save())
			{
				$this->redirect('/product/adminview/'.$model->id);
			}
		}
		$this->setViewVar('model',$model);
		$this->render();
	}
	
	public function adminviewAction()
	{
		$id = (int)$this->params['id'];
		$model = new productModel($id);
		$this->setViewVar('model',$model);
		$this->render();
	}
	
	public function viewAction()
	{
		$id = (int)$this->params['id'];
		$model = new productModel($id);
		$this->setViewVar('model',$model);
		$this->render();
	}
	
	public function addtocartAction()
	{
		
	}
	
	public function deleteAction()
	{
		
	}
	
	public function browseAction()
	{
		
	}
	
}