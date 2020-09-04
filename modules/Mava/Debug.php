<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 6/18/14
 * Time: 10:55 PM
 * To change this template use File | Settings | File Templates.
 */
class Mava_Debug
{
    /**
     * Private constructor. Use statically.
     */
    private function __construct()
    {
    }

    /**
     * Gets the debug HTML output. This is triggered by the _debug=1 URL parameter.
     *
     * @return string
     */
    public static function getDebugHtml()
    {
        if (Mava_Application::isRegistered('page_start_time'))
        {
            $pageTime = microtime(true) - Mava_Application::get('page_start_time');
        }
        else
        {
            $pageTime = 0;
        }

        $memoryUsage = memory_get_usage();
        $memoryUsagePeak = memory_get_peak_usage();

        if (Mava_Application::isRegistered('db'))
        {
            $dbDebug = self::getDatabaseDebugInfo(Mava_Application::getDb());
        }
        else
        {
            $dbDebug = array(
                'queryCount' => 0,
                'totalQueryRunTime' => 0,
                'queryHtml' => ''
            );
        }

        if ($pageTime > 0)
        {
            $dbPercent = ($dbDebug['totalQueryRunTime'] / $pageTime) * 100;
        }
        else
        {
            $dbPercent = 0;
        }

        $includedFiles = self::getIncludedFilesDebugInfo(get_included_files());

        $return = "<h1>Page Time: " . number_format($pageTime, 4) . "s</h1>"
            . "<h2>Memory: " . number_format($memoryUsage / 1024 / 1024, 4) . " MB "
            . "(Peak: " . number_format($memoryUsagePeak / 1024 / 1024, 4) . " MB)</h2>"
            . "<h2>Requests</h2>"
            . "<ul>"
            . "<li><b>Controller:</b> ". Mava_Application::$_controller .(Mava_Application::$_addon_controller != Mava_Application::$_controller ?" -> ". Mava_Application::$_addon_controller:"") ."</li>"
            . "<li><b>Action:</b> ". Mava_Application::$_action .(Mava_Application::$_addon_action != Mava_Application::$_action ?" -> ". Mava_Application::$_addon_action:"") ."</li>"
            . "<li><b>View:</b> ". Mava_Application::$_view ."</li>"
            . "<li><b>View Params:</b><div style='max-height: 400px;overflow-y: auto;background: #f4f4f4;padding: 0 20px; margin: 10px 0;'><xmp>". print_r(Mava_Application::$_view_params, true) ."</xmp></div></li>"
            . "</ul>"
            . "<h2>Queries ($dbDebug[queryCount], time: " . number_format($dbDebug['totalQueryRunTime'], 4) . "s, "
            . number_format($dbPercent, 1) . "%)</h2>"
            . $dbDebug['queryHtml']
            . "<h2>Included Files ($includedFiles[includedFileCount], Mava Classes: $includedFiles[includedMavaClasses])</h2>"
            . $includedFiles['includedFileHtml'];

        return $return;
    }

    public static function getDebugPageWrapperHtml($debugHtml)
    {
        return <<<DEBUG
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="robots" content="noindex" />
	<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
	<title>Mava Debug Output</title>
</head>
<body>
$debugHtml
</body>
</html>
DEBUG;

    }

    public static function getDatabaseDebugInfo(Mava_Database $db)
    {
        $return = array(
            'queryCount' => 0,
            'totalQueryRunTime' => 0,
            'queryHtml' => ''
        );

        $return['queryCount'] = $db->query_num;

        if ($return['queryCount'])
        {
            $return['queryHtml'] .= '<ol>';

            foreach ($db->querys AS $query)
            {
                $queryText = rtrim($query['str']);
                if (preg_match('#(^|\n)(\t+)([ ]*)(?=\S)#', $queryText, $match))
                {
                    $queryText = preg_replace('#(^|\n)\t{1,' . strlen($match[2]) . '}#', '$1', $queryText);
                }

                $explainOutput = '';

                if (preg_match('#^\s*SELECT\s#i', $queryText))
                {
                    $explainQuery = $db->query('EXPLAIN ' . $query['str']);
                    $explainRows = $explainQuery->rows;
                    if ($explainRows)
                    {
                        $explainOutput .= '<table border="1">'
                            . '<tr>'
                            . '<th>Select Type</th><th>Table</th><th>Type</th><th>Possible Keys</th>'
                            . '<th>Key</th><th>Key Len</th><th>Ref</th><th>Rows</th><th>Extra</th>'
                            . '</tr>';

                        foreach ($explainRows AS $explainRow)
                        {
                            foreach ($explainRow AS $key => $value)
                            {
                                if (trim($value) === '')
                                {
                                    $explainRow[$key] = '&nbsp;';
                                }
                                else
                                {
                                    $explainRow[$key] = htmlspecialchars($value);
                                }
                            }

                            $explainOutput .= '<tr>'
                                . '<td>' . $explainRow['select_type'] . '</td>'
                                . '<td>' . $explainRow['table'] . '</td>'
                                . '<td>' . $explainRow['type'] . '</td>'
                                . '<td>' . $explainRow['possible_keys'] . '</td>'
                                . '<td>' . $explainRow['key'] . '</td>'
                                . '<td>' . $explainRow['key_len'] . '</td>'
                                . '<td>' . $explainRow['ref'] . '</td>'
                                . '<td>' . $explainRow['rows'] . '</td>'
                                . '<td>' . $explainRow['Extra'] . '</td>'
                                . '</tr>';
                        }

                        $explainOutput .= '</table>';
                    }
                }

                $return['queryHtml'] .= '<li>'
                    . '<pre>' . htmlspecialchars($queryText) . '</pre>'
                    . '<div><strong>Run Time:</strong> ' . number_format($query['total_time'], 6) . '</div>'
                    . $explainOutput
                    . "</li>\n";

                $return['totalQueryRunTime'] += $query['total_time'];
            }

            $return['queryHtml'] .= '</ol>';
        }

        return $return;
    }

    public static function getIncludedFilesDebugInfo(array $includedFiles)
    {
        $return = array(
            'includedFileCount' => count($includedFiles),
            'includedFileHtml' => '<ol>',
            'includedMavaClasses' => 0
        );

        $baseDir = dirname(reset($includedFiles));

        foreach ($includedFiles AS $file)
        {
            $file = preg_replace('#^' . preg_quote($baseDir, '#') . '(\\\\|/)#', '', $file);
            $file = htmlspecialchars($file);

            if (preg_match('#^modules(/|\\\\)Mava(/|\\\\)#', $file))
            {
                $return['includedMavaClasses']++;
            }
            $file = preg_replace('#^modules(/|\\\\)Mava(/|\\\\)#', '<b>$0</b>', $file);

            $return['includedFileHtml'] .= '<li>' . $file . '</li>' . "\n";
        }
        $return['includedFileHtml'] .= '</ol>';

        return $return;
    }

    public static function getDebugTemplateParams()
    {
        $params = array();

        $pageUrl = Mava_Url::getCurrentAddress();
        $params['debug_url'] = $pageUrl . (strpos($pageUrl, '?') !== false ? '&' : '?') . '_debug=1';
        $params['phrase_text_url'] = $pageUrl . (strpos($pageUrl, '?') !== false ? '&' : '?') . '_phrase=1';
        $params['phrase_url'] = $pageUrl . (strpos($pageUrl, '?') !== false ? '&' : '?') . '_phrase=2';
        if (Mava_Application::isRegistered('page_start_time'))
        {
            $params['page_time'] = microtime(true) - Mava_Application::get('page_start_time');
        }

        $params['memory_usage'] = memory_get_usage();

        if (Mava_Application::isRegistered('db'))
        {
            $db = Mava_Application::getDb();
            $params['db_queries'] = $db->query_num;
        }

        return $params;
    }
}