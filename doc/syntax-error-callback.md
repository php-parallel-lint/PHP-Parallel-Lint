# Syntax Error Callback

1. Set a path to a file with custom error callback to `--syntax-error-callback` option.
1. Create a class implementing `PhpParallelLint\PhpParallelLint\Contracts\SyntaxErrorCallback` interface. File with the class must have the same name as the class inside.
1. Modify error before it is printed to the output.

## Example configuration

File `MyCustomErrorHandler.php` will be passed as an argument like `./parallel-lint --syntax-error-callback ./path/to/MyCustomErrorHandler.php .`.<br>
The content should look like:

```php

use PhpParallelLint\PhpParallelLint\Contracts\SyntaxErrorCallback;
use PhpParallelLint\PhpParallelLint\Errors\SyntaxError;

class MyCustomErrorHandler implements SyntaxErrorCallback {
	/**
     * @param SyntaxError $error
     * @return SyntaxError
     */
    public function errorFound(SyntaxError $error){
    	// Return new SyntaxError with custom modification to FilePath or Message
    	// Or return custom implementation of SyntaxError extending the original one...
    	return new SyntaxError(
    		$error->getFilePath(),
    		$error->getMessage()
    	);
    }
}
```
