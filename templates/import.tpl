{if $alertMessage =='' }
    <h3>Task1: import data via from xml file to MysqlDatabase</h3>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="XML">
        <label> Choose xml file to upload</label>
        <div style="padding:15px 0"> <input type="file" name="XML"></div>
        <button type="submit">Import</button>
    </form>
{else}
    {$alertMessage}
    <a href="index.php" >Back</a>
{/if}