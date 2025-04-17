{include file="header.tpl"}

<h3>Current User Information</h3>

<table>
    <tr>
        <th>Username</th>
        <td>{$username} ({$user})</td>
    </tr>
    <tr>
        <th>Name</th>
        <td>{$name}</td>
    </tr>
    <tr>
        <th>Email</th>
        <td>{$email}</td>
    </tr>
    <tr>
        <th>Account Type</th>
        <td>{$premium}</td>
    </tr>
    <tr>
        <th>Created</th>
        <td>{$created}</td>
    </tr>
</table>
{include file="footer.tpl"}