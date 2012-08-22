<?php
require_once 'php/facebook.php';
require_once 'config.php';

$name_search=$_GET['name_search'];

$user_id = $facebook->require_login();

$fb_user = $facebook->user;
//$facebook->api_client->profile_setFBML(NULL, $user, 'profile', NULL, NULL, 'profile_main');
$url="http://apps.facebook.com/turkbayragim/";
?>

<?php
   $message ="";
   $attachment = array( 
            'name' => 'T&uuml;rk Bayrağı - Profilinde', 
            'href' => 'http://apps.facebook.com/turkbayragim/', 
            'description' => 'Yeni Bir Rekor Kırmak İ&ccedil;in Daha Fazla Gecikme. Sende T&Uuml;RK BAYRAĞI Uygulamasını Ekle...',
            'media' => array(array('type' => 'image', 'src' => 'http://photos-h.ak.fbcdn.net/photos-ak-sf2p/v43/195/286499328135/app_1_286499328135_6781.gif', 'href' => 'http://apps.facebook.com/turkbayragim/')), 
            ); 
           $action_links = array( array('text' => 'Sende Ekle', 'href' =>   'http://apps.facebook.com/turkbayragim/')); 
           $attachment = json_encode($attachment); 
           $action_links = json_encode($action_links);
           $message = json_encode($message);
      ?>
<script>
<!--
function simpleFP(target) {
 var attachment = <?= $attachment ?>;
     var message = <?= $message ?>;
     var action_links = <?= $action_links ?>;

 Facebook.streamPublish(message,attachment,action_links,target);
}
</script>

<?

echo "<h1><fb:profile-pic uid='$user_id'></fb:profile-pic>Hi <fb:name linked='false' useyou='false' uid='$user_id'></fb:name>!</h1>Invite your Facebook friends to visit this hotspot with you!<BR><BR><BR>";

echo "<form>Type (a part of) your Facebook friend's name:<BR><input name='name_search' value='$name_search' type='text'><input name='url' value='$url' type='hidden'><INPUT TYPE=SUBMIT Name=SUBMIT Value='GO!'></Form><BR>";


$fql = "SELECT uid, name, first_name, pic_small FROM user WHERE strpos(lower(name),'$name_search') >= 0 AND uid IN (SELECT uid2 FROM friend WHERE uid1=".$user_id.") order by name";
//echo $fql;
$_friends = $facebook->api_client->fql_query($fql); 

$friends = array('name', 'pic_small', 'uid');


if (is_array($_friends) && count($_friends)) {
echo "<table border=0><tr>";
    foreach ($_friends as $friend) { 
        $i=$i+1;
        $friends[] = $friend['name'];
        $friends[] = $friend['pic_small'];
        $friends[] = $friend['uid'];
        echo "<td valign=top width=80 height=150 border=1><a href='#' onclick='simpleFP(".$friend['uid'].")'><img border=0  src=".$friend['pic_small']."><BR>".$friend['name']."</a></TD>";
        if($i % 8 == 0){
            echo "</tr><tr>";
            }
        }
echo "</tr></table>";
    } 
else
{
if ($name_search<>''){
print "Hiç Kimse Bulunamadı...";
}
}
?>

</html>