<?php
use Ovide\Libs\Mvc\Rest\Controller;
use Ovide\Libs\Mvc\Rest\Exception\NotFound;
use Ovide\Libs\Mvc\Rest\Exception\Conflict;
use Ovide\Libs\Mvc\Rest\Exception\Unauthorized;
abstract class MainRestController extends Controller {
	/**
	 * Return the model class for this controller
	 * Ex : return ModelExample::class
	 */
	protected abstract function getModelClass();
	protected function getModelCaption(){
		return $this->getModelClass();
	}
	/**
	 * Defines the primary key or the SQL condition to select a single record
	 * @param string $id
	 * @return string
	 */
	protected function getOneCriteria($id){
		return $id;
	}
	/**
	 * Define the copy from the object posted ($from) to the model object ($to)
	 * @param object $to
	 * @param object $from
	 */
	protected abstract function copyFrom($to,$from);

	protected function _isValidToken($token,$force=false){
		return $force=="true" || (isset($token) && $this->session->get("token")===$token);
	}

	protected function sendMessage($type,$content){
		return array("type"=>$type,"content"=>$content);
	}

	protected function infoMessage($message){
		return $this->sendMessage("info",$message);
	}

	protected function successMessage($message){
		return $this->sendMessage("success",$message);
	}
	protected function warningMessage($message){
		return $this->sendMessage("warning",$message);
	}
	protected function dangerMessage($message){
		return $this->sendMessage("danger",$message);
	}

	public function get(){
		$class=$this->getModelClass();
		$modelElements=$class::find();
		$modelElements=$modelElements->toArray();
		if(sizeof($modelElements)==0)
			throw new NotFound("Aucune "+$this->getModelCaption()+" trouvée");
		return $modelElements;
	}

	public function getOne($id){
		$class=$this->getModelClass();
		if (!$modelInstance = $class::findFirst($this->getOneCriteria($id)))
			throw new NotFound("Ooops! "+$this->getModelCaption()+" {$id} introuvable");
		return $modelInstance->toArray();
	}

	public function post($obj){
		$class=$this->getModelClass();
		if($this->_isValidToken($this->request->get("token"),$this->request->get("force"))){
			$modelInstance = new $class();
			$this->copyFrom($modelInstance, $obj);
			if($modelInstance->create()==false){
				throw new Conflict("Impossible d'ajouter '".$modelInstance."' dans la base de données.");
			}else{
				return array("data"=>$modelInstance->toArray(),"message"=>$this->successMessage("'".$modelInstance->getName()."' a été correctement ajoutée."));
			}
		}else{
			return array("message"=>"Vous n'avez pas les droits pour ajouter une instance de "+$this->getModelCaption());
		}
	}

	public function put($id, $obj){
		$class=$this->getModelClass();
		if($this->_isValidToken($this->request->get("token"),$this->request->get("force"))){
			$modelInstance = $class::findFirst($id);
			if(!$modelInstance){
				throw new NotFound("Mise à jour : '".$obj["name"]."' n'existe plus dans la base de données.");
				return array();
			}else{
				$this->copyFrom($modelInstance, $obj);
				try{
					$modelInstance->save();
					return array("data"=>$obj,"message"=>$this->successMessage("l'instance '".$obj["name"]."' a été correctement modifiée."));
				}
				catch(Exception $e){
					throw new Conflict("Impossible de modifier '".$obj["name"]."' dans la base de données.<br>".$e->getMessage());
				}
			}
		}else{
			throw new Unauthorized("Vous n'avez pas les droits pour modifier une instance de "+$this->getModelCaption());
		}
	}

	public function delete($id){
		$class=$this->getModelClass();
		if($this->_isValidToken($this->request->get("token"),$this->request->get("force"))){
			$modelInstance = $class::findFirst($id);
			if(!$modelInstance){
				return array("message"=>$this->warningMessage("Mise à jour : L'instance d'id '".$id."' n'existe plus dans la base de données."),"code"=>Response::UNAVAILABLE);
			}else{
				try{
					$modelInstance->delete();
					return array("data"=>$modelInstance->toArray(),"message"=>$this->successMessage("'".$modelInstance->getName()."' a été correctement supprimée."));
				}
				catch(Exception $e){
					throw new Conflict("Impossible de supprimer '".$modelInstance->getName()."' dans la base de données.<br>".$e->getMessage());
				}
			}
		}else{
			throw new Unauthorized("Vous n'avez pas les droits pour modifier une instance de "+$this->getModelCaption());
		}
	}
}