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

        $idProduct = (int) Tools::getValue('id_product');

        $questions = $this->getQuestionsForProduct($idProduct);

        $questionsHTML = '<h3>Questions about the product.</h3>';

        if(empty($questions)){
            $questionsHTML .= '<p>No questions yet!</p>';
        } else {
            foreach ($questions as $question){
                $questionsHTML .= '<div class="product-question">';
                $questionsHTML .= '<p><strong>Q:</strong> ' . htmlspecialchars($question['question']) . '</p>';

                if (!empty($question['answer'])) {
                    $questionsHTML .= '<p><strong>A:</strong> ' . htmlspecialchars($question['answer']) . '</p>';
                }

                $questionsHTML .= '</div>';
            }
        }
    
        # Notification handling
        $status = (string) Tools::getValue('question_status');
        $notificationHtml = '';

        switch($status){

        case 'success':
            $notificationHtml = '<div class="alert alert-success">
                Your question has been submitted for moderation.
            </div>';
            break;

        case 'invalid_question':
            $notificationHtml = '<div class="alert alert-danger">
                Question must contain between 1 and 1000 characters.
            </div>';
            break;

        case 'save_error':
            $notificationHtml = '<div class="alert alert-danger">
                Could not save your question.
            </div>';
            break;

        case 'invalid_token':
            $notificationHtml = '<div class="alert alert-danger">
                Invalid security token.
            </div>';
            break;

        default:
            break;
        }


        return $notificationHtml . '
            <form method="post" action="' . htmlspecialchars($action, ENT_QUOTES, 'UTF-8') . '">
                <input type="hidden" name="id_product" value=' . $idProduct . '>
                <input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') . '">
                <label for="question">Pytanie o produkt</label>
                <textarea id="question" name="question" required></textarea>
                <button type="submit" name="submitQuestion">
                    Wyślij pytanie
                </button>
            </form>
        ' . $questionsHTML;
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

        if(Tools::isSubmit('submitProductQuestionAnswer'))
        {
            $idQuestion = (int) Tools::getValue('id_product_question');
            $answer = trim((string) Tools::getValue('answer'));

            if($idQuestion <= 0 || $answer === ''){
                $html .= $this->displayError('Question ID and answer are required.');
            } else {
                $result = Db::getInstance()->update(
                    'product_question',
                    [
                        'answer' => pSQL($answer),
                        'is_approved' => 1,
                    ],
                    'id_product_question = ' . $idQuestion
                );

                if ($result){
                    $html .= $this->displayConfirmation("Answer saved.");
                } else {
                    $html .= $this->displayError("Failed to save the answer");
                }
            }
        }

        if(Tools::isSubmit('submitDeleteProductQuestion'))
        {
            $idQuestion = (int) Tools::getValue('id_product_question');
            $result = Db::getInstance()->delete('product_question', 'id_product_question = ' . $idQuestion);

            if($result){
                $html .= $this->displayConfirmation("Question deleted.");
            } else {
                $html .= $this->displayError("Failed to delete the question.");
            }
        }

        $questions = $this->getAllQuestions();

        $html .= '<h2>Product Questions</h2>';

        if(empty($questions)){
            return $html . '<p>No questions yet.</p>';
        }

        foreach($questions as $question){
            $html .= '
            <div class="panel">
                <p><strong>ProductID:</strong>' . (int) $question['id_product'] . '</p>
                <p><strong>Question:</strong>' . htmlspecialchars($question['question'], ENT_QUOTES, 'UTF-8') . '</p>
                <p><strong>Status:</strong>' . ((int) $question['is_approved'] === 1 ? 'Approved' : 'Pending') . '</p>
            
                <form method="post">
                    <input type="hidden" name="id_product_question" value="' . (int) $question['id_product_question'] . '">
                    <label>Answer</label>
                    <textarea name="answer" class="form-control" required>'
                    .htmlspecialchars((string) $question['answer'], ENT_QUOTES, 'UTF-8').
                    '</textarea>

                    <br>

                    <button type="submit"
                        name="submitProductQuestionAnswer"
                        class="btn btn-primary">
                        Save answer
                    </button> 

                    <button type="submit"
                    name="submitDeleteProductQuestion"
                    class="btn"
                    formnovalidate>
                        Delete
                    </button>
                </form>
            </div>';
        }

        return $html;
    }

}