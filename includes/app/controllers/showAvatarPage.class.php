<?php

class showAvatarPage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();
	}

	public function set()
	{
		if (user::get()->data['credits'] > 5)
		{
			$id = intval($_POST['avatar']);
			if ($id < 1 || $id > 56)
				$this->message("У вас нет подписки на этот аватар.", "Ошибка", "?set=avatar", 3);

			db::query("UPDATE game_users SET avatar = '" . $id . "', credits = credits - 5 WHERE id = " . user::get()->data['id'] . "");

			$this->message("Аватар успешно установлен.", "ОК", "?set=options", 3);
		}
		else
			$this->message("У вас не хватает средств для смены аватара.", "Ошибка", "?set=avatar", 3);
	}

	public function upload()
	{
		core::loadLib('upload');

		$upload = new upload($_FILES['image']);

		if ($upload->uploaded)
		{
			$upload->dir_auto_create = false;
			$upload->dir_auto_chmod = false;
			$upload->file_overwrite = true;
			$upload->file_max_size = 102400;
			$upload->mime_check = true;
			$upload->allowed = array('image/*');
			$upload->image_convert = 'jpg';
			$upload->image_resize = true;
			$upload->image_x = 128;
			$upload->image_y = 128;
			$upload->file_new_name_body = 'upload_' . user::get()->getId();

			$upload->Process('images/avatars/upload/');

			if (user::get()->data['credits'] > 5)
			{
				if ($upload->processed)
				{
					db::query("UPDATE game_users SET avatar = '99', credits = credits - 5 WHERE id = " . user::get()->getId() . "");
					$this->message("Аватар успешно установлен.", "ОК", "?set=options", 3);
				}
				else
					$this->message($upload->error, "Ошибка", "?set=avatar", 3);
			}
			else
				$this->message("У вас не хватает средств для смены аватара.", "Ошибка", "?set=avatar", 3);

			$upload->Clean();
		}
	}
	
	public function show ()
	{
		$html = "<script type='text/javascript'>function av(id){document.ava.src = '/images/avatars/'+id+'.jpg';}</script>";

		$html .= "<form action=\"?set=avatar&mode=set\" method=\"POST\"><table width=500><tr><td class=c colspan=2>Выбор аватара</td></tr>";
		$html .= "<tr><th colspan=2>Стоимость смены аватара - 5 кр.</th></tr>";
		$html .= "<tr><th width=30%><select name=avatar onchange=\"av(this.value)\">";

		for ($i = 1; $i < 57; $i++)
		{
			$html .= "<option value=" . $i . "";
			if (user::get()->data['avatar'] == $i)
				$html .= " selected";
			$html .= ">№ " . $i . "";
		}

		$html .= '</select></th><th><img src="/images/avatars/';

		if (user::get()->data['avatar'] != 0 && user::get()->data['avatar'] != 99)
			$html .= user::get()->data['avatar'];
		else
			$html .= "1";

		$html .= ".jpg\" name=ava width=100 height=100></th></tr><tr><td class=c colspan=2><input type=submit value=\"Сменить аватар\"></td></tr></table></form>";

		$html .= '<br><form name="form2" enctype="multipart/form-data" method="post" action="?set=avatar&mode=upload">
				<table width=500><tr><td class=c>Загрузка аватара</td></tr>
				<tr><th>Картинки уменьшаются до размера 128 на 128 пикселей<br><br>
		            <input type="file" size="32" name="image" value="" />
		            <input type="submit" name="Submit" value="Загрузить" /></th></tr></table>
		        </form>';

		$this->display($html, "Выбор аватара", false);
	}
}

?>