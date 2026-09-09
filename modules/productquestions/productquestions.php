<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductQuestions extends Module
{
    public function __construct()
    {
        $this->name = 'productquestions';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Damian';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Product Questions');
        $this->description = $this->l(
            'Allows customers to ask questions about products.'
        );
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayFooterProduct')
            && $this->installDatabase();
    }

    private function installDatabase()
    {
        return require __DIR__ . '/sql/install.php';
    }

    public function hookDisplayFooterProduct($params)
    {
        $action = $this->context->link->getModuleLink(
            'productquestions',
            'submit'
        );

        $idProduct = (int) Tools::getValue('id_product');

        $questions = $this->getQuestionsForProduct($idProduct);

        $questionsHTML = '<h3>Questions about the product.</h3>';

        if(empty($questions)){
            $questionsHTML .= '<p>No questions yet!</p>';
        } else {
            foreach ($questions as $question){
                $questionsHTML .= '
                <div class="product-question">
                    <p>' . htmlspecialchars($question['question'], ENT_QUOTES, 'UTF-8') . '</p>
                </div>';
            }
        }
    

        return '
            <form method="post" action="' . htmlspecialchars($action, ENT_QUOTES, 'UTF-8') . '">
                <input type="hidden" name="id_product" value=' . $idProduct . '>
                <label for="question">Pytanie o produkt</label>
                <textarea id="question" name="question" required></textarea>
                <button type="submit" name="submitQuestion">
                    Wyślij pytanie
                </button>
            </form>
        ' . $questionsHTML;
    }

    private function getQuestionsForProduct(int $idProduct):array
    {
        $sql = 'SELECT id_product_question, question, answer, date_add
        FROM `' . _DB_PREFIX_ .'product_question`
        WHERE id_product = ' . $idProduct . '
        ORDER BY date_add DESC;';

        return Db::getInstance()->executeS($sql);

    }

}