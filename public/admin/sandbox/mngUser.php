<?php
// header("content-type: text/html;charset=utf-8");
require_once('../../../includes/initialize.php');

// $loginUser = new LoginUser();



// $loginUser->login_user_id = 1;
// $loginUser->username = "leas";
// $loginUser->password = password_encrypt("!@#l30s=/");
// $loginUser->name = "ΔΗΜΗΤΡΙΟΣ";
// $loginUser->surname = "ΛΕΠΙΠΑΣ";
// $loginUser->role_id = 1;
// $loginUser->email = "lepipas@niometrics.com";
// $result = $loginUser->save();

// $loginUser->login_user_id = 1;
// $loginUser->username = "d.liapi";
// $loginUser->password = password_encrypt("!@#l10p1=/");
// $loginUser->name = "ΔΗΜΗΤΡΑ";
// $loginUser->surname = "ΛΙΑΠΗ";
// $loginUser->role_id = 2;
// $loginUser->email = "d.liapi@planetfitness.gr";
// $result = $loginUser->save();
$customer_name = "ΠΑΠΑΓΕΩΡΓΙΟΥ ΔΕΣΠΟΙΝΑ";

$customer_name = mb_strtoupper($customer_name, "UTF-8");
// echo $str; // Prints ΤΆΧΙΣΤΗ ΑΛΏΠΗΞ ΒΑΦΉΣ ΨΗΜΈΝΗ ΓΗ, ΔΡΑΣΚΕΛΊΖΕΙ ΥΠΈΡ ΝΩΘΡΟΎ ΚΥΝΌΣ

$gym_name = 'παλαιο ΦΑΛΗΡΟ';
$gym_name = mb_strtoupper($gym_name, "UTF-8");

echo "hello world!" . "<br/>";
echo strtoupper("hello world!") . "<br/>";
echo $customer_name . "<br/>";
echo 'ιοαννινα' . "<br/>";
$a = !empty(trim(1)) ? trim(1) : null;
echo "a: " . $a;
// $cms_id = 23;
// $result = Customer::check_cms_id($cms_id);
$result = Gym::find_by_primary_key(1);
// $result = Gym::find_by_foreign_key($gym_name);

echo "<pre>";
if ($result) {
    print_r($result);
// echo array_shift($result)->gym_id;
} else {
    echo "No results!";
}


echo "<pre/>";

// $loginUser->login_user_id = 2;
// $loginUser->username = "kgronthos";
// $loginUser->password = password_encrypt("!@#gr0nth0s=/");
// $loginUser->name = "Kostas";
// $loginUser->surname = "Gronthos";
// $loginUser->department_id = 2;
// $loginUser->email = "k.gronthos@srhmar.com";
// $loginUser->save();

// $loginUser->login_user_id = 3;
// $loginUser->username = "kseretis";
// $loginUser->password = password_encrypt("!@#s3r3t1s=/");
// $loginUser->name = "Kostas";
// $loginUser->surname = "Seretis";
// $loginUser->department_id = 2;
// $loginUser->email = "k.seretis@srhmar.com";
// $loginUser->save();

// $loginUser->login_user_id = 4;
// $loginUser->username = "gkourtelis";
// $loginUser->password = password_encrypt("!@#k00rt3l1s=/");
// $loginUser->name = "Giannis";
// $loginUser->surname = "Kourtelis";
// $loginUser->department_id = 2;
// $loginUser->email = "g.kourtelis@srhmar.com";
// $loginUser->save();

// $loginUser->login_user_id = 5;
// $loginUser->username = "mvikatos";
// $loginUser->password = password_encrypt("!@#v1k@t0s=/");
// $loginUser->name = "Michalis";
// $loginUser->surname = "Vikatos";
// $loginUser->department_id = 2;
// $loginUser->email = "m.vikatos@srhmar.com";
// $loginUser->save();

// $loginUser->login_user_id = 6;
// $loginUser->username = "dkosta";
// $loginUser->password = password_encrypt("!@#k0st@=/");
// $loginUser->name = "Dimitra";
// $loginUser->surname = "Kosta";
// $loginUser->department_id = 3;
// $loginUser->email = "d.kosta@srhmar.com";
// $loginUser->save();

//$loginUser->login_user_id = 23;
// $loginUser->username = "dlepipas";
// $loginUser->password = password_encrypt("!@#l3p1p@s=/");
// $loginUser->name = "Dimitrios";
// $loginUser->surname = "Lepipas";
// $loginUser->department_id = 4;
// $loginUser->email = "d.lepipas@srhmar.com";
//$loginUser->delete();
//echo $id['login_user_id'];

// $loginUsers = LoginUser::find_all();

// while(list($key, $user) = each($loginUsers)) {

// 	echo "<pre>";
// 	print_r($user);
// 	echo "<pre/>";
// }

// $message = "";
// //  // Remember to give your form's submit tag a name="submit" attribute


//   $username = "fparisis";
//   $password = "!@#p@r1s1s=/";

//   // check database to see if username/password exists.
//   $found_user_pwd = LoginUser::find_by_primary_key("fparisis");

//   echo "<pre>";
//   print_r($found_user_pwd);
//   echo "<pre/>";

  // if($found_user_pwd) {
  //   $existing_hash = $found_user_pwd->password;
  //   $hash = crypt($password, $existing_hash);

  //   $found_user = LoginUser::authenticate($username, $hash);
  //   if($found_vessel) {
  //     $session->login($found_vessel);
  //     echo "Logged in";
  //   } else {
  //       // username/password combo was not found in the database
  //       $message = "Username/password combination incorrect.";
  //     }
  // } else {
  //   $message = "Username does not exist.";
  // }



// SELECT pg_terminate_backend(pg_stat_activity.pid)
// FROM pg_stat_activity
// WHERE pg_stat_activity.datname = 'trinity'
//   AND pid <> pg_backend_pid();;
