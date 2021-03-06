# rest-phalcon-base
Minimalist API Rest based on https://github.com/ovide/phest

# howto

##Create models in `app/models`
Plain Old PHP Objects with getters and setters
```php
class Foo extends Model {
	private $key;
	private $title;
}
```
##Create controllers in `app/controllers`
Controllers must inherit from `\MainRestController` and implement :
 * getModelClass()
 * getOneCriteria($id)
 * copyFrom($to,$from)

```php
class Foos extends \MainRestController {
	/**
	* Return the model class for this controller
	* Ex : return ModelExample::class
	*/
	protected function getModelClass() {
		return Foo::class;
	}

	/**
	 * Defines the primary key or the SQL condition to select a single record
	 * @param string $id
	 * @return string
	 */
	protected function getOneCriteria($id) {
		return "key=".$id;
	}

	/**
	 * Define the copy from the object posted ($from) to the model object ($to)
	 * @param object $to
	 * @param object $from
	 */
	protected function copyFrom($to, $from) {
		$to->setKey($from["key"]);
		$to->setTitle($from["title"]);
	}
}
```
##Add resources

In `app/config/config.php` :

```php
<?php
return new \Phalcon\Config(array(
    'rest' => array('resources'=>array(Foos::class))
));
```

##Query the server

| Method     | URL           | Response   |
| ---------- |:------------- | :--------- |
| GET        | /foos         | All instances of `Foo` |
| GET        | /foos/12      | The Foo instance with id 12 |
| POST       | /foos         | Adds an instance of Foo using posted data |
| PUT        | /foos/12      | Updates the instance with id 12 using posted data |
| DELETE     | /foos/12      | Deletes the instance with id 12 |

##Possible extensions

###Custom query
If each instance of the Bar class has many Foos :

| Method     | URL           | Response   |
| ---------- |:------------- | :--------- |
| GET        | /foos/bar/11  | All instances of `Foo` from the `Bar` instance with id 11 |

In this case, we needs to had a method getBar in the Foos controller :

```php
    public function getBar($id){
    	$foos=Foo::find("idBar=".$id);
    	$foos=$foos->toArray();
    	if(sizeof($food)==0)
    		throw new NotFound("no Foos found.");
    	return $foos;
    }
 ```

###Custom operations
####Adding
| Method     | URL           | Response   |
| ---------- |:------------- | :--------- |
| POST        | /foos/bar/new  | Depends of the postBar() method implementation |

In this case, we needs to had a method `postBar`in the `Foos`controller :

```php
	public function postBar($id,$object){
		//Our implementation here;
		//$id is the last url parameter (new in our case)
		//$object is the posted object
	}
```

####Updating
| Method     | URL           | Response   |
| ---------- |:------------- | :--------- |
| PUT        | /foos/bar/11  | Depends of the putBar() method implementation |

In this case, we needs to had a method `putBar`in the `Foos`controller :

```php
	public function putBar($id,$object){
		//Our implementation here;
		//$id is the last url parameter (11 in our case)
		//$object contains the data send with the put method
	}
```

####Deleting
| Method     | URL           | Response   |
| ---------- |:------------- | :--------- |
| DELETE        | /foos/bar/11  | Depends of the deleteBar() method implementation |

In this case, we needs to had a method `deleteBar`in the `Foos`controller :

```php
	public function deleteBar($id,$object){
		//Our implementation here;
		//$id is the last url parameter (11 in our case)
		//$object contains the data send with the delete method
	}
```

###Key personalization

For the composite primary keys, it is necessary to override some methods in the controller :

Example :

```php
	protected function getOneCriteria($id) {
		$keys=explode("-", $id);
		return "idFoo='{$keys[0]}' AND idBar='{$keys[1]}'";
	}
```
