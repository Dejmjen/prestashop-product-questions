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

        $csrfToken = Tools::getToken(false);
        $idProduct = (int) $params['product']['id_product'];
        $questions = $this->getQuestionsForProduct($idProduct);
        $status = (string) Tools::getValue('question_status');

        $this->context->smarty->assign([
            'questions' => $questions,
            'action' => $action,
            'csrf_token' => $csrfToken,
            'id_product' => $idProduct,
            'question_status' => $status,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/product_questions.tpl');
    }

    private function getQuestionsForProduct(int $idProduct): array
    {
        $sql = 'SELECT id_product_question, question, answer, date_add
        FROM `' . _DB_PREFIX_ .'product_question`
        WHERE id_product = ' . $idProduct . '
        AND is_approved = 1
        ORDER BY date_add DESC;';

        $result = Db::getInstance()->executeS($sql);
        return is_array($result) ? $result : [];

    }

    private function getAllQuestions(): array
    {
        $sql = 'SELECT id_product_question, id_product, question, answer, is_approved, date_add
        FROM `' . _DB_PREFIX_ . 'product_question`
        ORDER BY date_add DESC;';

        $result = Db::getInstance()->executeS($sql);
        return is_array($result) ? $result : [];
    }

    public function getContent()
    {

        $html = '';

        # Perform operation depending on form action taken.
        if(Tools::isSubmit('submitProductQuestionAnswer'))
        { $html .= $this->handleAnswerSubmission(); }

        if(Tools::isSubmit('submitDeleteProductQuestion'))
        { $html .= $this->handleDeleteQuestion(); }

        $questions = $this->getAllQuestions();

        $this->context->smarty->assign([
            'questions' => $questions,
        ]);

        $html .= $this->display(__FILE__, 'views/templates/admin/configure.tpl');

        return $html;
    }

    private function handleAnswerSubmission():  string
    {
        $idQuestion = (int) Tools::getValue('id_product_question');
        $answer = trim((string) Tools::getValue('answer'));

        if ($idQuestion <= 0 || $answer === '')
        {return $this->displayError('Question ID and answer are required.');}

        $result = Db::getInstance()->update(
            'product_question',
            [
                'answer' => pSQL($answer),
                'is_approved' => 1,
            ],
            'id_product_question = ' . $idQuestion
        );

        return $result
            ? $this->displayConfirmation('Answer saved.')
            : $this->displayError('Failed to save the answer.');
    }

    private function handleDeleteQuestion(): string
    {
        $idQuestion = (int) Tools::getValue('id_product_question');

        if ($idQuestion <= 0){
            return $this->displayError('Invalid question ID.');
        }

        $result = Db::getInstance()->delete(
            'product_question',
            'id_product_question = ' . $idQuestion 
        );

        return $result
            ? $this->displayConfirmation('Question deleted.')
            : $this->displayError('Failed to delete the question.');
    }
}