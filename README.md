# rest-phalcon-base
Minimalist API Rest based on https://github.com/ovide/phest

# howto

##Create models in `app/models`
Plain Old PHP Objects with getters and setters
```php
class ModelExample extends Model {
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
class Contents extends \MainRestController {
	protected function getModelClass() {
		return Content::class;
	}


	/* (non-PHPdoc)
	 * @see MainRestController::getOneCriteria()
	 */
	protected function getOneCriteria($id) {
		$keys=explode("-", $id);
		return "key='{$keys[0]}' AND lang='{$keys[1]}'";
	}

	/**
	 * @var Content
	 * @var mixed
	 * @see MainRestController::copyFrom()
	 */
	protected function copyFrom($to, $from) {
		$to->setKey($from["key"]);
		$to->setLang($from["lang"]);
		$to->setTitle($from["title"]);
		$to->setValue($from["value"]);
	}
}
```
