{include file="header.tpl"}

<h3>Add a new action</h3>

<form role="form" action="/createAction/{$id}" method="post">

    <input type="radio" required id="move" name="option" value="move">Move to Notebook<br>
    <select name="moveNotebook">
        <option value="0">None</option>
        {section name=all loop=$notebooks}
            <option value="{$notebooks[all].guid}">{$notebooks[all].name}</option>
        {/section}
    </select>
    <br>
    <input type="radio" id="subject" name="option" value="subject">Change the Title<br>
    <input type="text" name="subjectFind" placeholder="Text to find">
    <input type="text" name="subjectReplace" placeholder="Text to replace">
    <br>
    <input type="radio" id="tags" name="option" value="tags">Add these tags<br>
    <small>You can use the following placeholders: <a href="#" id="year">{literal}{year}{/literal}</a>, <a href="#" id="month">{literal}{month}{/literal}</a>, <a href="#" id="day">{literal}{day}{/literal}</a>, <a href="#" id="dayord">{literal}{dayord}{/literal}</a>, <a href="#" id="dow">{literal}{dow}{/literal}</a>, <a href="#" id="date">{literal}{date}{/literal}</a></small>
    <input type="text" name="tags" id="tagText" placeholder="Comma separated list" >
    {if PUSHOVER_USER != ''}
        <br>
        <input type="radio" id="pushover" name="option" value="pushover">Send a notification to Pushover
    {/if}
    <br>
    <input type="radio" id="reminder" name="option" value="reminder">Set a Reminder
    <br>
    <input type="radio" id="delete" name="option" value="delete">Delete the note

    <p><input type="submit" value="Create action"></p>

</form>

{include file="footer.tpl"}