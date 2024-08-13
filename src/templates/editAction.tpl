{include file="header.tpl"}

<h3>Edit an action</h3>

<form role="form" action="/updateAction/{$id}/{$act}" method="post">

<input type="radio" required id="move" name="option" value="move" {if $option == 'move'}checked{/if}>Move to Notebook<br>
    <select name="moveNotebook">
        <option value="0">None</option>
        {section name=all loop=$notebooks}
            {if $notebooks[all].guid == $moveNotebook}
                <option value="{$notebooks[all].guid}" selected>{$notebooks[all].name}</option>
            {else}
                <option value="{$notebooks[all].guid}">{$notebooks[all].name}</option>
            {/if}
        {/section}
        </select>
    <br>
    <input type="radio" id="subject" name="option" value="subject" {if $option == 'subject'}checked{/if}>Change the Title<br>
    <input type="text" name="subjectFind" placeholder="Text to find" value="{$subjectFind}">
    <input type="text" name="subjectReplace" placeholder="Text to replace" value="{$subjectReplace}">
    <br>
    <input type="radio" id="tags" name="option" value="tags" {if $option == 'tags'}checked{/if}>Add these tags<br>
    <small>You can use the following placeholders: {literal}{year}, {month}, {day}, {dow}, {date}{/literal}</small>
    <input name="tags" id="tags" placeholder="Comma separated list" value="{$tags}">
    {if PUSHOVER_USER != ''}
        <br>
        <input type="radio" id="pushover" name="option" value="pushover" {if $option == 'pushover'}checked{/if}>Send a notification to Pushover
    {/if}
    <br>
    <input type="radio" id="delete" name="option" value="delete" {if $option == 'delete'}checked{/if}>Delete the note

    <p><input type="submit" value="Update action"></p>

</form>

{include file="footer.tpl"}