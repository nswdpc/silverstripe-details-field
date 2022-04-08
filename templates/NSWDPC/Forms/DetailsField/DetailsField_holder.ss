<details<% if $IsOpen %> open<% end_if %> class="detailsfield<% if $extraClass %> {$extraClass}<% end_if %>" id="{$HolderID}">
    <summary>
    <% if $Title %>{$Title}<% else %><%t DetailsField.DEFAULT_SUMMARY 'Details' %><% end_if %>
    <% include NSWDPC/Forms/DetailsField/Extras %>
    </summary>
    {$Field}
</details>
