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

If each instance of the Bar class has many Foos :
| Method     | URL           | Response   |
| ---------- |:------------- | :--------- |
| GET        | /foos/bar/11  | All instances of `Foo` from the `Bar` instance with id 11 |
