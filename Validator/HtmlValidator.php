<?php

class HtmlValidator {

	const VALIMATE_TEMPLATE = 'Template/Valimate.template';
	const VALIMATE_FILENAME = 'valimate.json';
	const SITEMAP_FILENAME = 'sitemap.xml';
	const REPORT_TEMPLATE = 'Template/Report.html';

	/**
	 * @var array
	 */
	private $specialCharacters = ['[1;37m', '[1;34m', '[1;31m', '[0m'];

	/**
	 * @var array
	 */
	private $args;

	/**
	 * @var String
	 */
	private $baseUrl;
	/**
	 * @var bool
	 */
	private $forceHard;

	/**
	 * @var String
	 */
	private $filename;

	/**
	 * @var String
	 */
	private $report;



	/**
	 * @param $args
	 */
	public function __construct($args) {
		$this->args = $args;

		$this->baseUrl = '/';
		if (isset($args['--baseUrl'])) {
			$this->baseUrl = $args['--baseUrl'];
		}

		$this->filename = 'html-validator.html';
		if (isset($args['--filename'])) {
			$this->filename = $args['--filename'];
		}

		$this->report = 'Report/' . $this->filename;
		if (isset($args['-o'])) {
			$this->report = rtrim($args['-o'], "/") . "/" . $this->filename;
		}
		if (isset($args['--output'])) {
			$this->report = rtrim($args['--output'], "/") . "/" . $this->filename;
		}

		$this->forceHard = false;
		if (isset($args['--force-hard'])) {
			$this->forceHard = ($args['--force-hard'] == 'true') ? true : false;
		}
		if (isset($args['-f'])) {
			$this->forceHard = ($args['-f'] == 'true') ? true : false;
		}
	}

	/**
	 * @return array
	 */
	private function getTemplate() {
		$jsonConfig = file_get_contents(self::VALIMATE_TEMPLATE);
		$template = json_decode($jsonConfig, true);
		return $template;
	}

	/**
	 * @return array
	 */
	private function getWebsiteUrls() {
		$urls = [];
		$sitemapUrl = rtrim($this->baseUrl, "/") . '/' . self::SITEMAP_FILENAME;
		$sitemap = simplexml_load_file($sitemapUrl);
		if ($sitemap) {
			foreach ($sitemap->url as $sitemapUrl) {
				$urls[] = rtrim((String) $sitemapUrl->loc, "/");
			}
		} else {
			echo sprintf("Could not find '%s'", $sitemapUrl);
		}
		return $urls;
	}

	/**
	 * @param $data
	 * @param $removeStrings
	 *
	 * @return mixed
	 */
	private function removeUnusedString($data, $removeStrings) {
		foreach ($removeStrings as $remove) {
			$data = str_replace($remove, "", $data);
		}
		return $data;
	}

	private function generateConfiguration() {
		$valimate = $this->getTemplate();
		$valimate['failHard'] = $this->forceHard;
		$urls = $this->getWebsiteUrls();
		if (is_array($urls) && count($urls)) {
			$valimate['urls'] = $urls;
		} else {
			$valimate['urls'] = [$baseUrl];
		}
		$this->createConfigurationFile($valimate);
	}

	/**
	 * @param $output
	 *
	 * @return array
	 */
	private function adjustResult($output) {
		$table = [];
		$index = -1;
		$reachError = false;
		foreach ($output as $string) {
			$string = trim($string);
			$string = $this->removeUnusedString($string, $this->specialCharacters);
			if ($string == '' | $string == "\n" || strpos($string, '▒') !== false || strpos($string, 'Validating URLs from valimate.json') !== false) {
				continue;
			}

			if (strpos($string, 'Results for') !== false) {
				$table[++$index] = [
					'no' => $index + 1,
					'url' => str_replace('Results for ', '', $string),
					'errors' => [],
					'warnings' => []
				];
				$reachError = false;
			} else if (strpos($string, 'errors:') !== false) {
				$reachError = true;
			} else {
				if ($reachError) {
					$table[$index]['errors'][] = $string;
				} else {
					$table[$index]['warnings'][] = $string;
				}
			}
		}
		return $table;
	}

	private function endMessage() {
		if (isset($this->args['-o'])) {
			echo sprintf("Check HTML validator report at '%s'", $this->report);
		} else {
			echo sprintf("Check HTML validator report at '%s'", __DIR__ . '/../' . $this->report);
		}
	}

	/**
	 * @param $output
	 */
	private function saveReport($output) {
		$reports = file_get_contents(self::REPORT_TEMPLATE);
		$reports = str_replace('###website###', $this->baseUrl, $reports);

		$adjustOutput = $this->adjustResult($output);
		$rows = '';
		$errors = 0;
		$warnings = 0;
		foreach ($adjustOutput as $data) {
			$error = count($data['errors']);
			$errors += $error;

			$warning = count($data['warnings']);
			$warnings += $warning;

			$rows .= '<tr><td>' . $data['no'] . '</td>';
			$rows .= '<td>' . $data['url'] . '</td>';
			$rows .= '<td><ul><li><strong>' . $error . '</strong> errors</li><li><strong>' . $warning . '</strong> warnings</li></ul></td>';

			$col = '<td>';
			$col .= '<h2>Errors:</h2><ol>';
			foreach ($data['errors'] as $errorMsg) {
				$col .= '<li>' . $errorMsg . '</li>';
			}
			$col .= '</ol>';

			$col .= '<h2>Warnings:</h2><ol>';
			foreach ($data['warnings'] as $warningMsg) {
				$col .= '<li>' . $warningMsg . '</li>';
			}
			$col .= '</ol></td>';

			$rows .= $col . '</tr>';
		}
		$reports = str_replace('###links###', count($adjustOutput), $reports);
		$reports = str_replace('###errors###', $errors, $reports);
		$reports = str_replace('###warnings###', $warnings, $reports);
		$rows = $this->removeUnusedString($rows, $this->specialCharacters);
		$reports = str_replace('###reports###', $rows, $reports);

		file_put_contents($this->report, $reports);

		$this->endMessage();
	}

	private function executeValidateCommand() {
		exec('valimate', $output, $status);
		if (0 == $status) {
			$this->saveReport($output);
		} else {
			echo "Command failed with status: $status";
		}
	}

	/**
	 * @param array $configuration
	 */
	private function createConfigurationFile($configuration) {
		file_put_contents(self::VALIMATE_FILENAME, $this->removeUnusedString(json_encode($configuration, JSON_PRETTY_PRINT), ["\\"]));
	}

	public function execute() {
		$this->generateConfiguration();
		$this->executeValidateCommand();
	}
}

?>
