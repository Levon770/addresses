<h1>Task2: Search in table</h1>
<form action="search.php" method="post" id="search">
    <label> Choose xml file to upload</label>
    <div style="padding:15px 0"><input type="text" name="Search" id="search_text"></div>
    <button type="submit" >SEARCH</button>
</form>

<div id="searchRes" style="display: none">

</div>
{literal}
<script type="text/javascript">
    var slct = $("#searchRes");

    $("#search").on("submit", function(e){
        e.preventDefault();
        slct.hide();
        slct.html('')
        $.post($(this).attr("action"), {search: $("#search_text").val()}, function(result){
            var res = JSON.parse(result);
            res.map(function(el, index){
                console.log(el)
                slct.append('<a href="view.php?id='+el.addresses_id+'">'+el.addresses_address+' '+el.addresses_street+'</a><br/>')
            })
            slct.show();
        } )
    });


</script>
{/literal}