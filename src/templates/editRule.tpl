{include file="header.tpl"}

<h3>Edit a rule</h3>
<form role="form" action="/updateRule/{$id}" method="post">

  <label class="col-lg-2 control-label">Rule name</label>
  <input type="text" name="ruleName" placeholder="rule name" value="{$ruleName}" required autofocus maxlength="100" size="50">

  <hr>
  <h5>When a note meets these conditions</h5>

  <label class="col-lg-2 control-label">When a Note is</label>
  <select name="type">
    {if $type == "Created"}
        <option selected>Created</option>
        <option>Updated</option>
    {else}
        <option>Created</option>
        <option selected>Updated</option>
    {/if}
  </select>

  <label class="col-lg-2 control-label">and is in Notebook</label>
  <select name="notebook">
    <option value="0">Any</option>
    {section name=all loop=$notebooks}
        {if $notebooks[all].guid == $notebookGuid}
            <option value="{$notebooks[all].guid}" selected>{$notebooks[all].name}</option>
        {else}
            <option value="{$notebooks[all].guid}">{$notebooks[all].name}</option>
        {/if}
    {/section}
  </select>
  <br>
  <small>The notebook chosen must be <a href="https://dev.evernote.com/support/faq.php#activatehook" target="_blank">registered with Evernote for webhooks</a>.</small>

  <label class="col-lg-2 control-label">and the Title</label>
  <select name="condition">
    {if $condition == 0}
        <option value="0" selected>Is anything</option>
    {else}
        <option value="0">Is anything</option>
    {/if}
    {if $condition == 1}
        <option value="1" selected>Is Equal to</option>
    {else}
        <option value="1">Is Equal to</option>
    {/if}
    {if $condition == 2}
        <option value="2" selected>Contains</option>
    {else}
        <option value="2">Contains</option>
    {/if}
    {if $condition == 3}
        <option value="3" selected>Starts with</option>
    {else}
        <option value="3">Starts with</option>
    {/if}
    {if $condition == 4}
        <option value="4" selected>Ends with</option>
    {else}
        <option value="4">Ends with</option>
    {/if}
  </select>
  <input type="text" name="conditionText" value="{$conditionText}" placeholder="Title text">

  <label class="col-lg-2 control-label">and the Author contains</label>
  <input type="text" name="authorText" value="{$authorText}" placeholder="Author text">

  <label class="col-lg-2 control-label">and has these Tags</label>
  <input name="tags" id="tags" value="{$tags}" placeholder="Comma separated tags" />

  <p><input type="submit" value="Update rule"></p>

</form>
<hr>
<h4>Actions</h4>
<figure>
<table style="width: 100%">
<thead>
    <tr>
        <th>Name</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    {section name=act loop=$actions}
    <tr>
        {if {$actions[act].option} == 'copy'}
            <td>Copy to {$actions[act].copyNotebookName}</td>
        {elseif {$actions[act].option} == 'move'}
            <td>Move to {$actions[act].moveNotebookName}</td>
        {elseif {$actions[act].option} == 'subject'}
            <td>Change the subject</td>
        {elseif {$actions[act].option} == 'tags'}
            <td>Add tags</td>
        {elseif {$actions[act].option} == 'pushover'}
            <td>Send a Pushover notification</td>
        {elseif {$actions[act].option} == 'delete'}
            <td>Delete the note</td>
        {/if}
        <td>
            <a href="/editAction/{$id}/{$smarty.section.act.index}">Edit</a>
			<a href="#" onclick="confirmRedirect('/deleteAction/{$id}/{$smarty.section.act.index}'); return false;">Delete</a>
        </td>
    </tr>
    {/section}
</tbody>
<tfoot>
    <tr>
        <th>Name</th>
        <th>Actions</th>
    </tr>
</tfoot>
</table>
<figcaption>Actions will be executed in the order shown</figcaption>
</figure>
<a class="button" href="/addAction/{$id}">Add a new action</a>


{include file="footer.tpl"}