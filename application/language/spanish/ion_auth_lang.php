<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Spanish
*
* Author: Wilfrido Garc�a Espinosa
* 		  contacto@wilfridogarcia.com
*         @wilfridogarcia
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  05.04.2010
*
* Description:  Spanish language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful'] 	  	 = ['0101','Cuenta creada con éxito'];
$lang['account_creation_unsuccessful'] 	 	 = ['0102','No se ha podido crear la cuenta'];
$lang['account_creation_duplicate_email'] 	 = ['0103','Email en uso o inválido'];
$lang['account_creation_duplicate_username'] = ['0104','Nombre de usuario en uso o inválido'];

// TODO Please Translate
$lang['account_creation_missing_default_group'] = ['0201','Default group is not set'];
$lang['account_creation_invalid_default_group'] = ['0202','Invalid default group name set'];

// Password
$lang['password_change_successful'] 	 	 = ['0301','Contraseña renovada con éxito. Debe iniciar sesión.'];
$lang['password_change_unsuccessful'] 	  	 = ['0302','No se ha podido cambiar la contraseña'];
$lang['forgot_password_successful'] 	 	 = ['0303','Nueva contraseña enviada por email'];
$lang['forgot_password_unsuccessful'] 	 	 = ['0304','No se ha podido crear una nueva contraseña'];
$lang['password_old_not_math'] 	 	         = ['0305','La contraseña actual no es correcta'];

// Activation
$lang['activate_successful'] 		  	     = ['0401','Cuenta activada con éxito'];
$lang['activate_unsuccessful'] 		 	     = ['0402','No se ha podido activar la cuenta'];
$lang['deactivate_successful'] 		  	     = ['0403','Cuenta desactivada con éxito'];
$lang['deactivate_unsuccessful'] 	  	     = ['0404','No se ha podido desactivar la cuenta'];
$lang['activation_email_successful'] 	  	 = ['0405','Email de activación enviado'];
$lang['activation_email_unsuccessful']   	 = ['0406','No se ha podido enviar el email de activación'];

// Login / Logout
$lang['login_successful'] 		      	     = ['0501','Sesión iniciada con éxito'];
$lang['login_unsuccessful'] 		  	     = ['0502','No se ha podido iniciar sesión. Correo o contraseña incorrecta.'];
$lang['login_unsuccessful_not_active'] 		 = ['0503','La cuenta está inactiva. Contacte al administrador del sistema.'];
$lang['login_timeout']                       = ['0504','Cuenta temporalmente bloqueda. Intente dentro de '];
$lang['logout_successful'] 		 	         = ['0505','Sesión finalizada con éxito'];

// Account Changes
$lang['update_successful'] 		 	         = ['0601','Información de la cuenta actualizada con éxito'];
$lang['update_unsuccessful'] 		 	     = ['0602','No se ha podido actualizar la información de la cuenta'];
$lang['delete_successful'] 		 	         = ['0603','Usuario eliminado'];
$lang['delete_unsuccessful'] 		 	     = ['0604','No se ha podido Eliminar el usuario'];

// Groups
$lang['group_creation_successful']           = ['0701','Group created Successfully'];
$lang['group_already_exists']                = ['0702','Group name already taken'];
$lang['group_update_successful']             = ['0703','Group details updated'];
$lang['group_delete_successful']             = ['0704','Group deleted'];
$lang['group_delete_unsuccessful']           = ['0705','Unable to delete group'];
$lang['group_delete_notallowed']             = ['0706','Can\'t delete the administrators\' group'];
$lang['group_name_required'] 		         = ['0707','Group name is a required field'];
$lang['group_name_admin_not_alter']          = ['0708','Admin group name can not be changed'];

// Activation Email
$lang['email_activation_subject']            = ['0801','Activación de la cuenta'];
$lang['email_activate_heading']              = ['0802','Activate account for %s'];
$lang['email_activate_subheading']           = ['0803','Please click this link to %s.'];
$lang['email_activate_link']                 = ['0804','Activate Your Account'];

// Forgot Password Email
$lang['email_forgotten_password_subject']    = ['0901','Verificación de contraseña olvidada'];
$lang['email_forgot_password_heading']       = ['0902','Reset Password for %s'];
$lang['email_forgot_password_subheading']    = ['0903','Please click this link to %s.'];
$lang['email_forgot_password_link']          = ['0904','Reset Your Password'];

// New Password Email
$lang['email_new_password_subject']          = ['1001','Nueva Contraseña'];
$lang['email_new_password_heading']          = ['1002','New Password for %s'];
$lang['email_new_password_subheading']       = ['1003','Your password has been reset to: %s'];
