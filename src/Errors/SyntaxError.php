<?php

namespace PHP_Parallel_Lint\PhpParallelLint\Errors;

use PHP_Parallel_Lint\PhpParallelLint\Blame;

class SyntaxError extends ParallelLintError
{
    const IN_ON_REGEX = '~ in %s on line [0-9]+$~';

    /** @var Blame */
    private $blame;

    /**
     * @return int|null
     */
    public function getLine()
    {
        preg_match('~on line ([0-9]+)$~', $this->message, $matches);

        if (isset($matches[1])) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * @param bool $translateTokens
     * @return mixed|string
     */
    public function getNormalizedMessage($translateTokens = false)
    {
        $message  = preg_replace('~^(?:Parse|Fatal) error: (?:syntax error, )?~', '', $this->message);
        $baseName = basename($this->filePath);
        $regex    = sprintf(self::IN_ON_REGEX, preg_quote($baseName, '~'));
        $message  = preg_replace($regex, '', $message, -1, $count);

        if ($count === 0 && strpos($baseName, '\\') !== false) {
            $baseName = ltrim(strrchr($this->filePath, '\\'), '\\');
            $regex    = sprintf(self::IN_ON_REGEX, preg_quote($baseName, '~'));
            $message  = preg_replace($regex, '', $message, -1, $count);
        }

        if ($count === 0) {
            $regex   = sprintf(self::IN_ON_REGEX, preg_quote($this->filePath, '~'));
            $message = preg_replace($regex, '', $message);
        }

        $message = ucfirst($message);

        if ($translateTokens) {
            $message = $this->translateTokens($message);
        }

        return $message;
    }

    /**
     * @param Blame $blame
     */
    public function setBlame(Blame $blame)
    {
        $this->blame = $blame;
    }

    /**
     * @return Blame
     */
    public function getBlame()
    {
        return $this->blame;
    }

    /**
     * @param string $message
     * @return string
     */
    protected function translateTokens($message)
    {
        static $translateTokens = array(
            'T_FILE' => '__FILE__',
            'T_FUNC_C' => '__FUNCTION__',
            'T_HALT_COMPILER' => '__halt_compiler()',
            'T_INC' => '++',
            'T_IS_EQUAL' => '==',
            'T_IS_GREATER_OR_EQUAL' => '>=',
            'T_IS_IDENTICAL' => '===',
            'T_IS_NOT_IDENTICAL' => '!==',
            'T_IS_SMALLER_OR_EQUAL' => '<=',
            'T_LINE' => '__LINE__',
            'T_METHOD_C' => '__METHOD__',
            'T_MINUS_EQUAL' => '-=',
            'T_MOD_EQUAL' => '%=',
            'T_MUL_EQUAL' => '*=',
            'T_NS_C' => '__NAMESPACE__',
            'T_NS_SEPARATOR' => '\\',
            'T_OBJECT_OPERATOR' => '->',
            'T_OR_EQUAL' => '|=',
            'T_PAAMAYIM_NEKUDOTAYIM' => '::',
            'T_PLUS_EQUAL' => '+=',
            'T_SL' => '<<',
            'T_SL_EQUAL' => '<<=',
            'T_SR' => '>>',
            'T_SR_EQUAL' => '>>=',
            'T_START_HEREDOC' => '<<<',
            'T_XOR_EQUAL' => '^=',
            'T_ECHO' => 'echo'
        );

        return preg_replace_callback('~(?<!\()T_([A-Z_]*)(?!\))~', function ($matches) use ($translateTokens) {
            list($tokenName) = $matches;
            if (isset($translateTokens[$tokenName])) {
                $operator = $translateTokens[$tokenName];
                return "$operator ($tokenName)";
            }

            return $tokenName;
        }, $message);
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return array(
            'type' => 'syntaxError',
            'file' => $this->getFilePath(),
            'line' => $this->getLine(),
            'message' => $this->getMessage(),
            'normalizeMessage' => $this->getNormalizedMessage(),
            'blame' => $this->blame,
        );
    }
}
