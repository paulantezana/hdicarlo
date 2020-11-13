<?php

class ExhibitorHistory extends Model
{
    public function __construct(PDO $connection)
    {
        parent::__construct('exhibitor_histories', 'exhibitor_history_id', $connection);
    }

    public function scrollByExhibitorId(int $exhibitorId,int $page = 1, int $limit = 20)
    {
        try {
            $offset = ($page - 1) * $limit;
    
            // Total pages
            $totalRows = $this->db->query('SELECT COUNT(*) FROM exhibitor_histories WHERE exhibitor_id = ' . "'". $exhibitorId."'")->fetchColumn();
            $totalPages = ceil($totalRows / $limit);

            $stmt = $this->db->prepare("SELECT exs.*, us.full_name as full_name FROM exhibitor_histories AS exs
                                        INNER JOIN users AS us ON exs.user_id = us.user_id
                                        WHERE exs.exhibitor_id = :exhibitor_id ORDER BY exhibitor_history_id DESC  LIMIT {$offset}, {$limit}");
            $stmt->bindParam(':exhibitor_id', $exhibitorId);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }
            $data = $stmt->fetchAll();

            return [
                'current' => $page,
                'pages' => $totalPages,
                'data' => $data,
                'more' => ($limit * $page) < $totalRows,
            ];
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }

    public function insert(array $exhibitorState, int $userId)
    {
        try {
            $currentDate = date('Y-m-d H:i:s');

            $stmt = $this->db->prepare("UPDATE exhibitor_histories set is_last = 0, updated_user_id = :updated_user_id, updated_at = :updated_at WHERE exhibitor_id = :exhibitor_id and is_last = 1;");
            $stmt->bindParam(':updated_at', $currentDate);
            $stmt->bindParam(':updated_user_id', $userId);
            $stmt->bindParam(':exhibitor_id', $exhibitorState['exhibitorId']);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }

            $stmt = $this->db->prepare('INSERT INTO exhibitor_histories (is_last, user_id, exhibitor_state, time_of_issue, exhibitor_id, created_at, created_user_id)
                                                    VALUES (:is_last, :user_id, :exhibitor_state, :time_of_issue, :exhibitor_id, :created_at, :created_user_id)');
            $stmt->bindValue(':is_last', 1);
            $stmt->bindValue(':user_id', $userId);
            $stmt->bindParam(':exhibitor_state', $exhibitorState['exhibitorState']);
            $stmt->bindParam(':time_of_issue', $currentDate);
            $stmt->bindParam(':exhibitor_id', $exhibitorState['exhibitorId']);

            $stmt->bindParam(':created_at', $currentDate);
            $stmt->bindParam(':created_user_id', $userId);

            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }

            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception('Error en metodo : ' . __FUNCTION__ . ' | ' . $e->getMessage());
        }
    }
}