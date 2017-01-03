<?php

namespace common\components;

use Yii;
use yii\validators\Validator;

/**
*
*
* @access Public
* @author Charlan Santos
*
* @package Component
* @since  09/2016
*
*
**/
class ValidationComponent extends Validator
{

    /**
     * @var array com os erros encontrados na validação -  errors (attribute name => array of errors)
     */
    private $_errors;

    /**
    * @var string categoria de tradução do módulo
    */
    public $tanslateCategory;

    /**
    * @var string mensagem de erro definida pelo programador de acordo com o ambiente: desenvolvimento ou produção
    */
    public $message;


    public function __construct ()
    {
        $this->tanslateCategory = 'app';
    }


    /**
    * Define a categoria de tradução.
    * Obrigatoriamente a mesma deverá existir no diretório messages
    *
    * @param string $translateCategory [opcional] - a categoria de tradução do módulo
    */
    public function setTranslateCategory($translateCategory)
    {
        if (!empty($translateCategory)) {
          $this->tanslateCategory = $translateCategory;
        }
    }

    public function getErrorMsgCurrentEnv($errorMsg, $defaultMessage = '')
    {
        $trace = debug_backtrace();
        $dbt = [];
        //Washington pegar o codigo do erro do amb. dev
        $string = $trace[0]['args'][0]['message']['dev'];
        $msg = explode(':',$trace[0]['args'][0]['message']['dev']);
        //print_r($msg[0]);die; //Ex: ORA-00001
        $dbt['file'] = $trace[1]['file'];
        $dbt['line'] = $trace[1]['line'];
        $dbt['function'] = $trace[1]['function'];
        $dbt['class'] = $trace[2]['class'];
        $dbt['codigo'] = $msg[0];

        $this->_errors['debugTrace'] = $dbt;
      return $this->setMessageByRule($errorMsg, $defaultMessage);
    }

    /**
     *
     *
     *
     * @param
     */
    public function isFalse($value, $messages = '', $translateCategory = 'app', $returnThrowException = false)
    {
        if ($value === '') {
            return false;
        }


        if (!isset($messages['dev'])) {
            if (is_array($value)) {
                $messages['dev'] = 'A variável <strong> {attribute} </strong> está com valor "false"';
            } else {
                $messages['dev'] = 'A variável está com valor "false"';
            }
        }


        if (!empty($messages)) {

            if (is_array($value)) {
                $keys = array_keys($value);
                $key = array_shift($keys);


                $value = $value[$key];

                $rule = [[[$key => $value], 'compare', 'operator' => '!=', 'compareValue' => false, 'type'=> 'number', 'message' => $messages]];

            } else {

                $rule = [[$value, 'compare', 'operator' => '!=', 'compareValue' => false, 'type'=> 'number', 'message' => $messages]];
            }
        }

        return $this->run($rule, $translateCategory, $returnThrowException);
    }

    /**
    * Retorna todos os erros encontrados ou erros de um atributo específico
    *
    * @param mixed $attribute [opcional] - Um atributo específico a ser retornado ou null para todos.
    * @return array - erros encontrados
    */
    public function getErrors($attribute = null)
    {
        if ($attribute === null) {
            return $this->_errors === null ? [] : $this->_errors;
        } else {
            return isset($this->_errors[$attribute]) ? $this->_errors[$attribute] : [];
        }
    }


    /**
     * Cria o objeto validador
     *
     * @param mixed $type o tipo do validador. Pode ser o apelido do validador (built-in validator name) vide yii\validators\Validator para a lista completa;,
     * ou um nome de um médodo que esta presente no objeto informado no parametro '$object' ou uma função anonima, ou o nome de uma classe que estende yii\validators\Validator
     * @param object [opcional]- Uma classe que contém o médoto informado no parametro type
     * @param array|string $attributes lista de atributos a serem validados ou string separado por virgula
     * @param array $params valores iniciais a serem aplicados nas propriedades do validador
     *
     * @return Validator - O validator
     */
    public static function createValidator($type, $object = null, $attributes, $params = [])
    {
        $params['attributes'] = $attributes;

        $objectMethodExist = (method_exists($object, $type)) ? true: false;

        if ($type instanceof \Closure || $objectMethodExist) {
            // method-based validator
            $params['class'] = __NAMESPACE__ . '\InlineValidator';
            $params['method'] = $type;
        } else {
            if (isset(static::$builtInValidators[$type])) {
                $type = static::$builtInValidators[$type];
            }
            if (is_array($type)) {
                $params = array_merge($type, $params);
            } else {
                $params['class'] = $type;
            }
        }

        return Yii::createObject($params);
    }

    /**
     * Executa a validação de uma varíavel da mesma forma que o método Model->validate()
     * porém, não é necessário informar um modelo. Dessa forma é possivel utilizar qualquer
     * Validator de yii\validators de maneira desacoplada de um Model.
     *
     * exemplo básico de utilização:
     *
     *  $result = Yii::$app->validationComponent->run(
     *        [
     *          [[ 'returnQuery' => $query, 'id' => $id], 'required'],
     *          [['COR10_COD_MATERIAL' => $codmat], 'string', 'max' => 1],
     *      ], 'app')->getErrors();
     *
     * É possível também informar dois tipos de mensagem:
     *  - uma que será exibida em ambiente de desenvolvimento YII_ENV_DEV
     *  - outra que será exibida em ambiente de produção YII_ENV_PROD
     *
     *  ex:  $result = Yii::$app->validationComponent->run(
     *	        [
     *  	      [[ 'returnQuery' => $query, 'id' => $id], 'required', 'message' =>['dev' => 'A variável {attribute} está vazia custom dev', 'prod' => 'asdasd {attribute} mensagem custom prod']],
     *            [['COR10_COD_MATERIAL' => $codmat], 'string', 'max' => 1, 'message' =>['prod' => '{attribute} mensagem custom prod']],
     *          ], 'app');
     *
     * É possível ainda retornar os erros como string separados por quebras de linha atráves de uma
     * thow exception que poderá ser capturada num bloco try{}catch{}. Para isso informe o 3° parametro com true
     *
     *  ex: Yii::$app->validationComponent->run(
     *	        [
     *  	      [[ 'returnQuery' => $query, 'id' => $id], 'required', 'message' =>['dev' => 'A variável {attribute} está vazia custom dev', 'prod' => 'asdasd {attribute} mensagem custom prod']]
     *          ], 'app', true);
     *
     *
     * @param array $rules - array rules similar ao retorno do metodo rules() do Model
     * @param string $tanslateCategory - categoria da tradução presente no diretório message o padrão é app
     * @param boolean $returnThowExc [opcional] - boleano que ativa o retorno de erros através de thow Exception()
     * @param object $object [opcional] - objeto que irá conter o metodo personalizado de validação. Para maiores detalhes vide
     * a documentação do Model->rules()
     * @return mixed - alimenta o atributo $this_erros que podem ser recuperados através do médoto getErrors()
     * ou retorna os erros separados por quebra de linha através de thow new Execption
     */
    public function run($rules, $tanslateCategory, $returnThowExc = false, $object = null)
    {
        $this->getDebugBackTrace();

        $this->setTranslateCategory($tanslateCategory);

        foreach ($rules as $rule) {

            $validator = self::createValidator($rule[1], $object, (array) $rule[0], array_slice($rule, 2));

            foreach ($validator->attributes as $attribute => $value) {
                $result = $validator->validateValue($value);

                if (!empty($result)) {

                    $this->setMessageByRule($rule, $result[0]);

                    $this->addError($attribute, $value, $this->message , $result[1]);
                }
            }
        }

        if ($this->hasError()) {
            if ($returnThowExc) {
                $stringErrors = $this->getErrorsAsString();
                throw new \Exception($stringErrors);
            }
        }

        return $this;
     }


     /**
      * Verifica se existe erros atrachados no atributo $this->_errors
      *
      * @return boolean
      */
     public function hasError()
     {
         return isset($this->_errors['attributes']);
     }

     /**
      * Obtém os erros como string separado por quebras de linha
      *
      * @return string - string com os erros
      */
     public function getErrorsAsString()
     {
         $stringErros = '';
         $errors = $this->getErrors();

         if (empty($errors)) {
             return false;
         }

         foreach ($errors['attributes'] as $error) {
             $stringErros .= $error. '\r\n';
         }

         return $stringErros;
     }




    /**
     * Define a mensagem de erro conforme chave 'message' definida no array rules
     *
     * @param array $rule - uma rule igual é utilizado no modelo ex: [['COR10_COD_MATERIAL'], 'required']
     * @return string - alimenta o atributo $this->error e ao mesmo tempo retorna a string de erro.
     */
    private function setMessageByRule(&$rule, $defaultMessage = '')
    {
        if (YII_ENV_DEV) {

            $this->message = $this->getDefaultHeaderMessage();

            if (!empty($rule['message']['dev'])) {

              return $this->message .= $rule['message']['dev'];
            }

            unset($rule['message']);

           return  $this->message .= $defaultMessage['dev'];

        } else {

            if (!empty($rule['message']['prod'])) {

                return $this->message = $rule['message']['prod'];
            }

            unset($rule['message']);
           return  $this->message = $this->getDefaultMessageProd();
        }
    }

    /**
     *
     *
     * @param
     * @return
     */
    private function getDefaultHeaderMessage()
    {
        $method = $this->_errors['debugTrace']['function'];
        $class = $this->_errors['debugTrace']['class'];
        $line = $this->_errors['debugTrace']['line'];

        return "
                Erro no método:<br>
                <strong> $method() </strong> <br><br>
                Classe: <br> <strong> $class </strong> <br><br>
                Linha: <strong> $line. </strong> <br><br>
                Descrição do erro: <br>
        ";
    }

    /**
     *
     *
     * @param
     * @return
     */
    private function getDefaultMessageProd()
    {
        return 'Erro ao executar a operação, entre em contato com o suporte técnico.';

    }

    /**
     * Adiciona um erro, com suporte a tradução i18n, para cada variável informada no array de Rules
     *
     * @param string $attribute - o nome da variável
     * @param string $value - o conteudo da variável
     * @param string $message - a mensagem de erro a ser exibida
     * @param array $params - array que será usado na substituição de placeholder {attribute}
     *
     * ex $params['attribute'] = 'id'
     *    $params['value'] =  '50'
     *    ele monta a seguinte: no lugar de {attribute} insere o valor de $params['attribute']
     *
     * @return void - alimenta o atributo $this->error
     */
    public function addError($attribute, $value, $message, $params = [])
    {
        $params['attribute'] = $attribute;

        if (!isset($params['value'])) {
            $params['value'] = $value;
        }

        $this->_addError($attribute, Yii::$app->getI18n()->format($this->tanslateCategory, $message, $params, Yii::$app->language));
    }

    /**
     * Adiciona um novo erro para uma variável específica
     *
     * @param string $attribute nome da variável
     * @param string $error - mensagem de erro
     *
     * @return void - alimenta o atributo $this->error
     */
    private function _addError($attribute, $error = '')
    {
        $this->_errors['attributes'][$attribute] = $error;
    }

    private function getDebugBackTrace()
    {
        $dbt = [];
        $trace = debug_backtrace();

        $dbt['file'] = $trace[2]['file'];
        $dbt['line'] = $trace[2]['line'];
        $dbt['function'] = $trace[2]['function'];
        $dbt['class'] = $trace[3]['class'];

       return $this->_errors['debugTrace'] = $dbt;
    }

}
