<?php
/**
* Plugin Name: SimpleNews
* Plugin URI: 
* Description: This plugin adds a widget that shows your latest news.
* Version: 1.0.0
* Author: Javier Suarez - Suarz Solutions
* Author URI: http://www.javiersuarezsanchez.com
* License: 
*/

function showSimpleNews(){
    $array_news = get_news();
    $newsperrow = get_option('SimpleNewsperRow');
    $bootstrapwidth = 12/$newsperrow;
    $rows = ceil(get_option('SimpleNewsTotal')/get_option('SimpleNewsperRow'));
    echo '<div class="container newscontainer">';
    echo '<div class="row">';
    echo '<div class="col-xs-12">';
    echo '<div class="newstitle">';
    echo '<h3>Últimas noticias</h3>';
    echo '</div></div></div>';
    $j=0;
    $limite=get_option('SimpleNewsperRow');
    for($i=0; $i<$rows; $i++){
        echo '<div class="row">';
        while(($j<$limite)&&($j<get_option('SimpleNewsTotal'))){
            echo '<div class="col-xs-12 col-sm-6 col-md-'.$bootstrapwidth.'">';
            echo '<div class="row">';
            echo '<div class="col-xs-12">';
            echo '<img src="'.$array_news[$j][2].'" class="img-responsive">';
            echo '</div>';
            echo '</div>';
            echo '<div class="row">';
            echo '<div class="col-xs-12 newtext">';
            echo '<p>'.$array_news[$j][1].'</p>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            $j++;}
        $limite=$limite+get_option('SimpleNewsperRow');
        echo '</div>';}}
        
//New table dedicated to SimpleNews News
function newNewsTable(){
    global $wpdb;
    $table_name = $wpdb->prefix . "Simplenews";
    $sql = " CREATE TABLE $table_name(
        NewsID int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        NewsText VARCHAR(1000) NOT NULL,
        NewsImageUrl VARCHAR(1000) NOT NULL
        ) ;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);}

//Insert new
function add_new(){
    global $wpdb;
    $wpdb->insert($wpdb->prefix."Simplenews", array('NewsText' => $_POST['text'], 'NewsImageUrl' => $_POST['imageurl']));}

function delete_new($id){
    global $wpdb;
    $wpdb->delete($wpdb->prefix."SimpleNews", array('NewsID' => $id));}

function get_news(){
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."Simplenews;" , ARRAY_N);}

function get_var(){
    global $wpdb;
    return $wpdb->get_var("SELECT * FROM `".$wpdb->prefix."Simplenews` WHERE NewsId;" , ARRAY_N);}

//////////////////////////////////////////////
//    TRHOW NEW TABLE WHEN PLUGIN IS ACTIVATED
//////////////////////////////////////////////

register_activation_hook(__FILE__,'newNewsTable');

//Create AdminMenuPage
function simplenews_admin_menuoptions(){
    add_menu_page('SimpleNews admin','SimpleNews','read','miplugin-ops','simplenews_admin_page');}

/////////////////////////////
//    THROW NEW ADMINMENUPAGE
/////////////////////////////

add_action('admin_menu','simplenews_admin_menuoptions');

//AdminMenuPage form structure
function simplenews_admin_page(){
    echo "<div class='wrap'><h2>SimpleNews Manage Panel</h2></div>";
    if(isset($_POST['salvarnoticia']) && ($_POST['salvarnoticia'] == "salvarnoticia")){
        add_new();
        echo("<div class='updated message' style='padding: 10px'>New notice added.</div>");}
    if(isset($_POST['borrarnoticia']) && ($_POST['borrarnoticia'] == "borrarnoticia")){
        delete_new($_POST['deleteSelection']);}
    if(isset($_POST['salvaropciones']) && ($_POST['salvaropciones'] == "salvaropciones")){
        update_option('SimpleNewsTotal', $_POST['totalnews']);
        if((12%$_POST['newsperrow'])==0){
            update_option('SimpleNewsperRow', $_POST['newsperrow']);
        }else{
            echo("<div class='error message' style='padding: 10px'>Value not valid.</div>");}}
    $array_news = get_news();
?>
    <form method='post'>
        <input type='hidden' name='salvarnoticia' value='salvarnoticia'> 
        <h3>Add a new:</h3>
        <table>
            <tr>
                <td><textarea type='text' rows="4" cols="80" name='text' id='text' value='<? $text?>' placeholder="Type text of your notice"></textarea></td>
            </tr>
            <tr>
                <td><input id="upload_image" type="text" name="imageurl" value="" placeholder="Copypaste image URL"/> </td>
            </tr>
            <tr>
                <td><input type='submit' value='Add new'></td>
            </tr>
        </table>
    </form>
    <br><br>
    <form method='post'>
        <input type='hidden' name='borrarnoticia' value='borrarnoticia'>
        <h3>Current news:</h3>
        <table class="tablestyle">
            <tr>
                <th class="thstyle">Id</th>
                <th class="thstyle">Text</th>
                <th class="thstyle">ImageURL</th>
                <td class="tdstyle">Delete</td>
            </tr>
            
            <?php
            for($i=0; $i<count($array_news); $i++){
                echo "<tr class='trstyle'>";
                for($j=0; $j<count($array_news[$i]); $j++){
                    $id=$array_news[$i][0];
                        echo "<td class='tdstyle'>".$array_news[$i][$j]."</td>";}
                echo "<td class='tdstyle'><input type='radio' name='deleteSelection' value='".$id."'></td>";
                echo "</tr>";}
                echo "<tr><td></td><td></td><td></td><td><input type='submit' value='Delete selection'></td></tr>";
            ?>
        
        </table>
    </form>
    <br><br><br>
    <form method='post'>
        <input type='hidden' name='salvaropciones' value='salvaropciones'> 
        <h3>View options:  </h3>
        <table>
            <tr>
                <td><p>Total visible news:</p></td>
                <td><input type="text" id="totalnews" name="totalnews" value="<?php echo get_option('SimpleNewsTotal'); ?>" placeholder="Number of visible news."><td> 
            </tr>
            <tr>
                <td><p>Visible news per row (1,2,3,4,6 or 12):</p></td>
                <td><input type="text" id="newsperrow" name="newsperrow" value="<?php echo get_option('SimpleNewsperRow')?>" placeholder="News per row"></td>
            </tr>
            <tr>
                <td></td>
                <td><input type='submit' value='Update options'></td>
            </tr>
        </table>
    </form>
        
<?php
    }
    function adminHeadAdds(){
        echo "<link rel='stylesheet' type='text/css' href='".plugins_url()."/SimpleNews/css/simplenewsstyle.css'/>";}
    function headAdds(){
        echo "<link rel='stylesheet' type='text/css' href='".plugins_url()."/SimpleNews/css/simplenewsstyle.css'/>";}

    add_action('admin_head', 'adminHeadAdds');
    add_action('wp_head', 'headAdds');
?>
