<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

/**
 * Database Compatibility Helper
 * 
 * Provides database-agnostic SQL functions that work with both
 * PostgreSQL and MySQL. This centralizes all database-specific
 * syntax in one place for easy maintenance.
 * 
 * Usage: use App\Helpers\DbHelper;
 *        DbHelper::groupConcat('column_name', ', ')
 */
class DbHelper
{
    /**
     * Get the current database driver name
     */
    public static function driver(): string
    {
        return DB::connection()->getDriverName();
    }

    /**
     * Check if using PostgreSQL
     */
    public static function isPostgres(): bool
    {
        return self::driver() === 'pgsql';
    }

    /**
     * Check if using MySQL
     */
    public static function isMysql(): bool
    {
        return in_array(self::driver(), ['mysql', 'mariadb']);
    }

    /**
     * GROUP_CONCAT equivalent (PostgreSQL uses STRING_AGG)
     * 
     * @param string $column Column to concatenate
     * @param string $separator Separator between values (default: ', ')
     * @param string|null $orderBy Optional ORDER BY clause
     * @param string $alias Result column alias
     */
    public static function groupConcat(
        string $column,
        string $separator = ', ',
        ?string $orderBy = null,
        string $alias = 'concatenated'
    ): \Illuminate\Database\Query\Expression {
        if (self::isPostgres()) {
            // PostgreSQL: STRING_AGG(column, separator ORDER BY ...)
            $order = $orderBy ? " ORDER BY {$orderBy}" : '';
            return DB::raw("STRING_AGG({$column}::TEXT, '{$separator}'{$order}) as {$alias}");
        } else {
            // MySQL: GROUP_CONCAT(column ORDER BY ... SEPARATOR '...')
            $order = $orderBy ? " ORDER BY {$orderBy}" : '';
            return DB::raw("GROUP_CONCAT({$column}{$order} SEPARATOR \"{$separator}\") as {$alias}");
        }
    }

    /**
     * CAST to integer (PostgreSQL doesn't support UNSIGNED)
     * 
     * @param string $column Column to cast
     */
    public static function castInt(string $column): string
    {
        // Both PostgreSQL and MySQL support CAST(x AS INTEGER)
        // MySQL's UNSIGNED is not supported in PostgreSQL
        return "CAST({$column} AS INTEGER)";
    }

    /**
     * Order by time components (hour and minute columns)
     * 
     * @param string $hourCol Hour column name
     * @param string $minuteCol Minute column name  
     * @param string $direction ASC or DESC
     */
    public static function orderByTime(
        string $hourCol = 'hour',
        string $minuteCol = 'minute',
        string $direction = 'ASC'
    ): string {
        $castHour = self::castInt($hourCol);
        $castMinute = self::castInt($minuteCol);
        return "{$castHour} {$direction}, {$castMinute} {$direction}";
    }

    /**
     * Calculate total minutes from hour and minute columns
     * Useful for time comparisons
     * 
     * @param string $hourCol Hour column name
     * @param string $minuteCol Minute column name
     */
    public static function totalMinutes(string $hourCol = 'hour', string $minuteCol = 'minute'): string
    {
        $castHour = self::castInt($hourCol);
        $castMinute = self::castInt($minuteCol);
        return "({$castHour}*60 + {$castMinute})";
    }

    /**
     * IFNULL/COALESCE - Returns first non-null value
     * MySQL uses IFNULL, PostgreSQL uses COALESCE (both support COALESCE)
     * 
     * @param string $column Column to check
     * @param mixed $default Default value if null
     */
    public static function ifNull(string $column, $default): string
    {
        // COALESCE works in both MySQL and PostgreSQL
        return "COALESCE({$column}, {$default})";
    }

    /**
     * Random order (MySQL: RAND(), PostgreSQL: RANDOM())
     */
    public static function random(): string
    {
        return self::isPostgres() ? 'RANDOM()' : 'RAND()';
    }

    /**
     * Date formatting
     * MySQL: DATE_FORMAT(col, '%Y-%m-%d')
     * PostgreSQL: TO_CHAR(col, 'YYYY-MM-DD')
     */
    public static function dateFormat(string $column, string $format): string
    {
        if (self::isPostgres()) {
            // Convert MySQL format to PostgreSQL
            $pgFormat = str_replace(
                ['%Y', '%m', '%d', '%H', '%i', '%s'],
                ['YYYY', 'MM', 'DD', 'HH24', 'MI', 'SS'],
                $format
            );
            return "TO_CHAR({$column}, '{$pgFormat}')";
        }
        return "DATE_FORMAT({$column}, '{$format}')";
    }
}
