{include file="header.tpl"}

<h3>Add a new rule</h3>
<form role="form" action="/createRule" method="post">

  <label class="col-lg-2 control-label">Rule name</label>
  <input type="text" name="ruleName" placeholder="rule name" required autofocus maxlength="100" size="50">

  <hr>
  <h5>When a note meets these conditions</h5>

  <label class="col-lg-2 control-label">When a Note is</label>
  <select name="type">
    <option>Created</option>
    <option>Updated</option>
  </select>

  <label class="col-lg-2 control-label">and is in Notebook</label>
  <select name="notebook">
    <option value="0">Any</option>
    {section name=all loop=$notebooks}
      <option value="{$notebooks[all].guid}">{$notebooks[all].name}</option>
    {/section}
  </select>
  <br>
  <small>The notebook chosen must be <a href="https://dev.evernote.com/support/faq.php#activatehook" target="_blank">registered with Evernote for webhooks</a>.</small>

  <label class="col-lg-2 control-label">and the Title</label>
  <select name="condition">
    <option value="0">Is anything</option>
    <option value="1">Is Equal to</option>
    <option value="2">Contains</option>
    <option value="3">Starts with</option>
    <option value="4">Ends with</option>
  </select>
  <input type="text" name="conditionText" placeholder="Title text">

  <label class="col-lg-2 control-label">and the Author contains</label>
  <input type="text" name="authorText" placeholder="Author text">

  <label class="col-lg-2 control-label">and has these Tags</label>
  <input name="tags" id="tags" placeholder="Comma separated tags" />

  <p><input type="submit" value="Create rule"></p>

</form>


{include file="footer.tpl"}