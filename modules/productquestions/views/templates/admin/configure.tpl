<div class="panel">
    <h2>Product Questions</h2>

    {if empty($questions)}
        <p>No questions yet.</p>
    {else}

        {foreach from=$questions item=question}

            <div class="panel">

                <p>
                    <strong>ProductID:</strong>
                    {$question.id_product|intval}
                </p>

                <p>
                    <strong>Question:</strong>
                    {$question.question|escape:'html':'UTF-8'}
                </p>

                <p>
                    <strong>Status:</strong>
                    {if $question.is_approved|intval === 1}
                        Approved
                    {else}
                        Pending
                    {/if}
                </p>

                <form method="post">
                    <input
                        type="hidden"
                        name="id_product_question"
                        value="{$question.id_product_question|intval}"
                    >

                    <label>Answer</label>

                    <textarea
                        name="answer"
                        class="form-control"
                        required
                    >{$question.answer|escape:'html':'UTF-8'}</textarea>

                    <br>

                    <button
                        type="submit"
                        name="submitProductQuestionAnswer"
                        class="btn btn-primary"
                    >
                        Save answer
                    </button>

                    <button
                        type="submit"
                        name="submitDeleteProductQuestion"
                        class="btn btn-danger"
                        formnovalidate
                        style="margin-left: 8px;"
                    >
                        Delete
                    </button>
                </form>
            </div>
        {/foreach}
    {/if}
</div>