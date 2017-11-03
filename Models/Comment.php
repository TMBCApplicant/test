<?php
/**
 * Hands the Comment data model
 */

include_once __DIR__.'/../Helpers/DB.php';

class Comment {

    protected $id = 0;
    protected $name = '';
    protected $text = '';
    protected $parentId = 0;

    protected $dbConnection;

    /**
     * Comment constructor. Initis core DB connection
     */
    public function __construct()
    {
        $this->dbConnection = new DB();
    }

    /**
     * returns Name of commenter
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets Name of commenter
     *
     * @param string $name
     *
     * @return string
     */
    public function setName(string $name): string
    {
        $this->name = $name;
        return $this->name;
    }

    /**
     * Returns text message of the comment
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Sets text message of the comment
     *
     * @param $text
     *
     * @return string
     */
    public function setText($text): string
    {
         $this->text = $text;
         return $this->text;
    }

    /**
     * Returns parent Id of the comment
     *
     * @return int
     */
    public function getParentId(): int
    {
        return $this->parentId;
    }

    /**
     * Sets the parent Id of the comment
     *
     * @param int $parentId
     *
     * @return int
     */
    public function setParentId(int $parentId): int
    {
        $this->parentId = $parentId;
        return $this->parentId;
    }

    /**
     * Returns the Id of the comment
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Sets the Id of the comment.
     * This is protected to only allow the Id to come from the database
     *
     * @param int $id
     *
     * @return int
     */
    protected function setId(int $id): int
    {
        $this->id = $id;
        return $this->id;
    }

    /**
     * Returns how deeply the comment is nested (1st = 1, reply = 2, reply-to-reply = 3)
     * This function is costly for resources and would need caching in a real environment if an graph DB was not used
     *
     * @param $id
     * @param int $count
     *
     * @return int
     */
    public function getNestLevel($id, $count=1) {
        $whereConditionArray = [[
            'column' => 'id',
            'operator' => '=',
            'value' => $id
        ]];

        $sqlResultArray = $this->dbConnection->simpleSelect('comments', ['*'], $whereConditionArray);
        if (empty($sqlResultArray[0]['parent_id'])) {
            return $count;
        }

        $count++;
        return $this->getNestLevel($sqlResultArray[0]['parent_id'], $count);
    }

    /**
     * Fetches comments in their nested heirarchy. A parent Id can be supplied to narrow the results returned.
     *
     * @param int|null $parentId
     * @param array $valueArray
     * @param array $processIdArray
     *
     * @return array
     */
    public function fetchComments(int $parentId = null, array &$valueArray = [], array &$processIdArray = []): array
    {
        $whereConditionArray = [];
        if($parentId != null) {
            $whereConditionArray = [[
                'column' => 'parent_id',
                'operator' => '=',
                'value' => $parentId
            ]];
        }

        $sqlResultArray = $this->dbConnection->simpleSelect('comments', ['*'], $whereConditionArray, 'create_date ASC');
        foreach($sqlResultArray as $result) {
            if (in_array($result['id'], $processIdArray)) {
                continue;
            }

            $processIdArray[] = $result['id'];
            $result['children'] = [];
            $this->fetchComments($result['id'], $result['children'], $processIdArray);
            $valueArray[] = $result;
        }

        return $valueArray;
    }

    /**
     * Saves the model to the database.
     *
     * @return bool
     */
    public function save(): bool
    {
        $queryData = [
            'name' => $this->getName(),
            'text' => $this->getText(),
        ];

        if ($this->getParentId()) {
            $queryData['parent_id'] = $this->getParentId();
        }

        $recordId = $this->dbConnection->insertRecord($queryData, 'comments');
        $this->setId($recordId);

        return true;
    }

}