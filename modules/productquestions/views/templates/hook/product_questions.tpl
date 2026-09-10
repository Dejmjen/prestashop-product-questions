{if $question_status === 'success'}
    <div class="alert alert-success">
        Your question has been submitted for moderation.
    </div>
{elseif $question_status === 'invalid_question'}
    <div class="alert alert-danger">
        Question must contain between 1 and 1000 characters.
    </div>
{elseif $question_status === 'save_error'}
    <div class="alert alert-danger">
        Could not save your question.
    </div>
{elseif $question_status === 'invalid_token'}
    <div class="alert alert-danger">
        Invalid security token.
    </div>
{/if}

<form method="post" action="{$action|escape:'html':'UTF-8'}">

    <input
        type="hidden"
        name="id_product"
        value="{$id_product|intval}"
    >

    <input
        type="hidden"
        name="csrf_token"
        value="{$csrf_token|escape:'html':'UTF-8'}"
    >

    <label for="question">Question about the product</label>

    <textarea
        id="question"
        name="question"
        required
    ></textarea>

    <button
        type="submit"
        name="submitQuestion"
    >
        Send question
    </button>

</form>

<h3>Questions about the product</h3>

{if empty($questions)}
    <p>No questions yet!</p>
{else}
    {foreach from=$questions item=question}
        <div class="product-question">

            <p>
                <strong>Q: </strong>
                {$question.question|escape:'html':'UTF-8'}
            </p>

            {if !empty($question.answer)}
                <p>
                    <strong>A: </strong>
                    {$question.answer|escape:'html':'UTF-8'}
                </p>
            {/if}
        </div>
    {/foreach}
{/if}
