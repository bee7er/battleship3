
<span class="bs-text" style="cursor:cell;margin-right: 8px;" onclick="copyLinkToClipboard()" title="Copy to clipboard">{{$game->player_two_link}}</span>
<img style="cursor:cell;" onclick="copyLinkToClipboard()" src="{{env("BASE_URL", "/")}}images/clipboard.jpg" width="18px" title="Copy to clipboard" />
<input type="text" value="{{$game->player_two_link}}" id="copyInp" style="position:absolute;left:-1000px;top:-1000px;">

<script type="text/javascript">
    /**
     * Copies a link to the clipboard
     */
    function copyLinkToClipboard() {
        var copyText = document.getElementById("copyInp");
        copyText.select();
        document.execCommand("copy"); //this function copies the text of the input with ID "copyInp"
        // Notify the user this has happened
        showNotification("Link copied to the clipboard");
    }
</script>
