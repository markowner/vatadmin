<?php

namespace plugin\vatadmin\app\processor;

class TraceIdProcessor
{
    public function __invoke(array $record): array
    {
        $traceId = \support\Context::get('trace_id', '');
        if ($traceId) {
            $record['extra']['trace_id'] = $traceId;
        }
        return $record;
    }
}