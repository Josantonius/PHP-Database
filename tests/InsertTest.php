<?php
/**
 * SQL database management to be used by several providers at the same time.
 *
 * @author    Josantonius <hello@josantonius.com>
 * @copyright 2017 - 2018 (c) Josantonius - PHP-Database
 * @license   https://opensource.org/licenses/MIT - The MIT License (MIT)
 * @link      https://github.com/Josantonius/PHP-Database
 * @since     1.1.6
 */
namespace Josantonius\Database;

use PHPUnit\Framework\TestCase;

/**
 * Test class for "INSERT" query.
 */
final class InsertTest extends TestCase
{
    /**
     * Database instance.
     *
     * @since 1.1.7
     *
     * @var object
     */
    private $db;

    /**
     * Setup.
     *
     * @since 1.1.7
     */
    public function setUp()
    {
        parent::setUp();

        $this->db = Database::getConnection(
            'identifier',
            'PDOprovider',
            $GLOBALS['DB_HOST'],
            $GLOBALS['DB_USER'],
            $GLOBALS['DB_NAME'],
            $GLOBALS['DB_PASSWORD'],
            ['charset' => 'utf8']
        );
    }

    /**
     * [QUERY] [RETURN ROWS AFFECTED]
     */
    public function testInsertReturnRows()
    {
        $result = $this->db->query(
            'INSERT INTO test_table (name, email)
             VALUES ("Isis", "isis@email.com")'
        );

        $this->assertSame(1, $result);
    }

    /**
     * [QUERY] [RETURN LAST INSERT ID]
     */
    public function testInsertReturnID()
    {
        $result = $this->db->query(
            'INSERT INTO test_table (name, email)
             VALUES ("Isis", "isis@email.com")',
            false,
            'id'
        );

        $this->assertSame(2, $result);
    }

    /**
     * [QUERY] [STATEMENTS] [RETURN ROWS AFFECTED]
     */
    public function testInsertStatementsReturnRows()
    {
        $statements[] = [':name', 'Isis'];
        $statements[] = [':email', 'isis@email.com'];

        $result = $this->db->query(
            'INSERT INTO test_table (name, email)
             VALUES (:name, :email)',
            $statements
        );

        $this->assertSame(1, $result);
    }

    /**
     * [QUERY] [STATEMENTS] [DATA-TYPE] [RETURN ROWS AFFECTED]
     */
    public function testInsertStatementsDataTypeReturnRows()
    {
        $statements[] = [':id', 3008, 'int'];
        $statements[] = [':name', 'Isis', 'str'];
        $statements[] = [':email', 'isis@email.com', 'str'];

        $result = $this->db->query(
            'INSERT INTO test_table (id, name, email)
             VALUES (:id, :name, :email)',
            $statements
        );

        $this->assertSame(1, $result);
    }

    /**
     * [QUERY] [STATEMENTS] [DATA-TYPE] [RETURN ROWS AFFECTED] [EXCEPTION]
     *
     * @expectedException \Josantonius\Database\Exception\DBException
     *
     * @expectedExceptionMessage Duplicate entry
     */
    public function testInsertDuplicateEntryException()
    {
        $statements[] = [':id', 3008, 'int'];
        $statements[] = [':name', 'Isis', 'str'];
        $statements[] = [':email', 'isis@email.com', 'str'];

        $result = $this->db->query(
            'INSERT INTO test_table (id, name, email)
             VALUES (:id, :name, :email)',
            $statements
        );
    }

    /**
     * [QUERY] [MARKS STATEMENTS] [RETURN LAST INSERT ID]
     */
    public function testInsertStatementsReturnID()
    {
        $statements[] = [1, 'Isis'];
        $statements[] = [2, 'isis@email.com'];

        $result = $this->db->query(
            'INSERT INTO test_table (name, email)
             VALUES (?, ?)',
            $statements,
            'id'
        );

        $this->assertSame(3009, $result);
    }

    /**
     * [QUERY] [MARKS STATEMENTS] [DATA-TYPE] [RETURN LAST INSERT ID]
     */
    public function testInsertStatementsDataTypeReturnID()
    {
        $statements[] = [1, 4883, 'int'];
        $statements[] = [2, 'Isis', 'str'];
        $statements[] = [3, 'isis@email.com', 'str'];

        $result = $this->db->query(
            'INSERT INTO test_table (id, name, email)
             VALUES (?, ?, ?)',
            $statements,
            'id'
        );

        $this->assertSame(4883, $result);
    }

    /**
     * [QUERY] [EXCEPTION]
     *
     * @expectedException \Josantonius\Database\Exception\DBException
     *
     * @expectedExceptionMessageRegExp (table|view|not|found|exist|Table)
     */
    public function testInsertTableNameErrorException()
    {
        $result = $this->db->query(
            'INSERT INTO xxxx (name, email)
             VALUES ("Isis", "isis@email.com")'
        );
    }

    /**
     * [QUERY] [EXCEPTION]
     *
     * @expectedException \Josantonius\Database\Exception\DBException
     *
     * @expectedExceptionMessageRegExp (Column|not|found|Unknown|column)
     */
    public function testInsertColumnNameErrorException()
    {
        $result = $this->db->query(
            'INSERT INTO test_table (xxxx, email)
             VALUES ("Isis", "isis@email.com")'
        );
    }

    /**
     * [METHOD] [RETURN ROWS AFFECTED]
     */
    public function testInsertMethodReturnRows()
    {
        $data = [
            'name' => 'Isis',
            'email' => 'isis@email.com',
        ];

        $query = $this->db->insert($data)
                          ->in('test_table');

        $result = $query->execute();

        $this->assertSame(1, $result);
    }

    /**
     * [METHOD] [STATEMENTS] [RETURN LAST INSERT ID]
     */
    public function testInsertMethodStatementsReturnID()
    {
        $statements[] = [':name', 'Isis', 'str'];
        $statements[] = [':email', 'isis@email.com', 'str'];

        $data = [
            'name' => ':name',
            'email' => ':email',
        ];

        $query = $this->db->insert($data, $statements)
                          ->in('test_table');

        $result = $query->execute('id');

        $this->assertSame(4885, $result);
    }

    /**
     * [METHOD] [STATEMENTS] [DATA-TYPE] [RETURN LAST INSERT ID]
     */
    public function testInsertMethodStatementsDataTypeReturnID()
    {
        $statements[] = [':name', 'Isis'];
        $statements[] = [':email', 'isis@email.com'];

        $data = [
            'name' => ':name',
            'email' => ':email',
        ];

        $query = $this->db->insert($data, $statements)
                          ->in('test_table');

        $result = $query->execute('id');

        $this->assertSame(4886, $result);
    }

    /**
     * [METHOD] [MARKS STATEMENTS] [RETURN LAST INSERT ID]
     */
    public function testInsertMethodMarksStatementsReturnID()
    {
        $statements[] = [1, 'Isis'];
        $statements[] = [2, 'isis@email.com'];

        $data = [
            'name' => '?',
            'email' => '?',
        ];

        $query = $this->db->insert($data, $statements)
                          ->in('test_table');

        $result = $query->execute('id');

        $this->assertSame(4887, $result);
    }

    /**
     * [METHOD] [MARKS STATEMENTS] [DATA-TYPE] [RETURN ROWS AFFECTED]
     */
    public function testInsertMethodMarksDataTypeReturnRows()
    {
        $statements[] = [1, 'Isis', 'str'];
        $statements[] = [2, 'isis@email.com', 'str'];

        $data = [
            'name' => '?',
            'email' => '?',
        ];

        $query = $this->db->insert($data, $statements)
                          ->in('test_table');

        $result = $query->execute();

        $this->assertSame(1, $result);
    }

    /**
     * [METHOD] [EXCEPTION]
     *
     * @expectedException \Josantonius\Database\Exception\DBException
     *
     * @expectedExceptionMessageRegExp (table|view|not|found|exist|Table)
     */
    public function testInsertMethodTableNameErrorException()
    {
        $data = [
            'name' => 'Isis',
            'email' => 'isis@email.com',
        ];

        $query = $this->db->insert($data)
                          ->in('xxxx');

        $result = $query->execute();
    }

    /**
     * [METHOD] [EXCEPTION]
     *
     * @expectedException \Josantonius\Database\Exception\DBException
     *
     * @expectedExceptionMessageRegExp (Column|not|found|Unknown|column)
     */
    public function testInsertMethodColumnNameErrorException()
    {
        $data = [
            'xxxx' => 'Isis',
            'email' => 'isis@email.com',
        ];

        $query = $this->db->insert($data)
                          ->in('test_table');

        $result = $query->execute();
    }
}
