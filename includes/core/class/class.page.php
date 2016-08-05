<?php

/**
* Класс отображения страницы
* P.S. Кто будет выводить что-то в обход этого класса, тому посылаю лучи ненависти))
* @author AlexPro
* @copyright 2011 - 2013
* ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
*/

class page
{
// META
public $attributes = array('title' => SITE_TITLE);
// Выбор подключаемой шапки и подвала
public $header		= '';
// Название последнего инициализированного шаблона
public $template	= '';
// Выводимый контент (Выводится в центральной части, после показа всех шаблонов)
public $page		= '';
public $pagePos		= 'bottom';
// Выводить как ajax контент (без шапки и подвала)
public $ajax 		= false;
/**
 * Глобальный объект template
 * @var template
 */
public $tmpl;
/**
 * Объекты HSTemplateDisplay
 * @var $templates templateDisplay[]
 */
public $templates = array();

/**
 * Инициализируем класс шаблонизатора
 */
function __construct()
{
	$this->tmpl = new template(array('template_path' => ROOT_DIR.'template/'.core::getConfig('gameTemplate'), 'cache_path' => ROOT_DIR.CACHE_DIR, 'debug' => core::getConfig('DEBUG')));

	$this->globals('isAjax', 	(isset($_REQUEST['ajax'])));
	$this->globals('isPopup', 	(isset($_REQUEST['popup'])));
	$this->globals('userId',	user::get()->getId());

	if (user::get()->isAuthorized())
		$this->globals('userName',	user::get()->data['username']);

	if (isset($_REQUEST['ajax']))
		$this->ajax = true;

	if (!defined('TEMPLATE_PATH'))
		define('TEMPLATE_PATH', '/template/'.core::getConfig('gameTemplate'));
}

/**
    * Основная функция генерации страницы
    * @param array $pageParams
    */
public function display($pageParams = array())
{
	$this->globals('pageParams', $pageParams);

	// Вывод шапки страницы
	$DisplayFrames = $this->tmpl->getDisplay('frame');
	$DisplayFrames->addTemplate('header', $this->header.'header.php');
	$DisplayFrames->assign('attributes', $this->attributes, 'header');
	$DisplayFrames->display();

	if ($this->page != '' && $this->pagePos == 'top')
		echo $this->page;

	// Вывод центральной части страницы из подшаблонов
	if (count($this->templates) > 0)
	{
		foreach ($this->templates AS $template)
		{
			$template->display();
		}
	}

	// Вывод обычного контента
	if ($this->page != '' && $this->pagePos == 'bottom')
		echo $this->page;

	// Вывод подвала сайта
	$DisplayFrames = $this->tmpl->getDisplay('frame');
	$DisplayFrames->addTemplate('footer', $this->header.'footer.php');
	$DisplayFrames->assign('attributes', $this->attributes, 'footer');
	$DisplayFrames->display();

	die();
}

public function setAtribute ($name, $content)
{
	$this->attributes[$name] = $content;
}

public function getAtribute ($name)
{
	return (isset($this->attributes[$name]) ? $this->attributes[$name] : '');
}

/**
 * Передача переменной в шаблон
 * @param string $key Ключ
 * @param mixed $value Значение
 */
public function set ($key, $value)
{
	if ($this->template != '')
		$this->templates[$this->template]->assign($key, $value, $this->template);
}

/**
 * Передача глабальной переменной в шаблон.
 * Глобальные переменные доступны во всех шаблонах данного класса
 * @param string $key Ключ
 * @param mixed $value Значение
 */
public function globals ($key, $value)
{
	$this->tmpl->assignGlobal($key, $value);
}

/**
 * Добавление нового шаблона для вывода
 * @param string $name Имя нового шаблона
 * @return bool
 */
public function setTemplate ($name)
{
	if (!isset($this->templates[$name]))
	{
		$this->template 		= $name;
		$this->templates[$name] = $this->tmpl->getDisplay('pages');
		$this->templates[$name]->addTemplate($name, $name.'.php');

		return true;
	}

	return false;
}

/**
 * Переключение вывода на другой шаблон (шаблон должен быть уже инициализирован ранее)
 * @param string $name Имя существующего шаблона
 * @return bool
 */
public function setTemplateName ($name)
{
	if (isset($this->templates[$name]))
	{
		$this->template = $name;

		return true;
	}

	return false;
}

/**
 * Вывод блока с контентом. Используется для выода новостей, меню и прочего мини-контента.
 * @param string $name Название блока
 * @param array $params Дополнительные параметры
 * @return string HTML контент блока
 */
public function ShowBlock ($name, $params = array())
{
	ob_start();
	include_once(ROOT_DIR.APP_PATH."block/".$name.".php");
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}
}

?>