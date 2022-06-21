<?php
namespace ESOS\GraphQL;

ini_set('display_errors', 'Off');

use Exception;

use GraphQL\Error\DebugFlag;
use GraphQL\GraphQL;
use GraphQL\Utils\BuildSchema;

use ESOS\GraphQL\SchemaHelper;

class Bootstrap {
  public static function run($rawInput) {
    $log_inout = !empty($_ENV['LOG_INOUT']);
    $sdl = file_get_contents(__DIR__ . '/schema.graphql');
    $schema = BuildSchema::build($sdl, SchemaHelper::typeConfigDecorator());

    try {
      $input = json_decode($rawInput, true);

      if ($log_inout) {
        logger($_SERVER, '$_SERVER');
        logger($input, 'INPUT');
      }

      $query = empty($input['query']) ? null : $input['query'];
      $variableValues = empty($input['variables']) ? null : $input['variables'];
      $results = GraphQL::executeQuery(
        $schema,
        $query,
        SchemaHelper::rootValue(),
        null,
        $variableValues,
      );

      if (empty($results)) {
        http_response_code(400);
        echo 'Bad request';
        exit();
      }

      $output =
        !empty($_ENV['ENVIRONMENT']) && $_ENV['ENVIRONMENT'] === 'development'
          ? $results->toArray(
            DebugFlag::INCLUDE_DEBUG_MESSAGE |
              DebugFlag::RETHROW_INTERNAL_EXCEPTIONS,
          )
          : $results->toArray();
    } catch (Exception $e) {
      $output = [
        'error' => [
          'message' => $e->getMessage(),
        ],
      ];

      if (!empty($_ENV['GRAPHQL_LOGS'])) {
        $output['code'] = $e->getCode();
        $output['file'] = $e->getFile();
        $output['line'] = $e->getLine();
        $output['trace'] = explode("\n", $e->getTraceAsString());
      }
    }

    if ($log_inout) {
      logger($output, 'OUTPUT');
    }

    echo json_encode($output);
  }
}
