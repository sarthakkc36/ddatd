<?php
class Team {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllActiveMembers() {
        $sql = "SELECT * FROM team_members WHERE is_active = 1 ORDER BY display_order ASC";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching team members: " . $e->getMessage());
            return [];
        }
    }

    public function getMemberById($id) {
        $sql = "SELECT * FROM team_members WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching team member: " . $e->getMessage());
            return null;
        }
    }

    public function createMember($data) {
        $sql = "INSERT INTO team_members (name, position, bio, photo_path, specialties, qualifications, is_active, display_order) 
                VALUES (:name, :position, :bio, :photo_path, :specialties, :qualifications, :is_active, :display_order)";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'name' => $data['name'],
                'position' => $data['position'],
                'bio' => $data['bio'],
                'photo_path' => $data['photo_path'] ?? null,
                'specialties' => $data['specialties'] ?? null,
                'qualifications' => $data['qualifications'] ?? null,
                'is_active' => $data['is_active'] ? 1 : 0,
                'display_order' => $data['display_order'] ?? 0
            ]);
        } catch (PDOException $e) {
            error_log("Error creating team member: " . $e->getMessage());
            return false;
        }
    }

    public function updateMember($id, $data) {
        $sql = "UPDATE team_members SET 
                name = :name, 
                position = :position, 
                bio = :bio, 
                specialties = :specialties, 
                qualifications = :qualifications, 
                is_active = :is_active, 
                display_order = :display_order";
        
        if (isset($data['photo_path'])) {
            $sql .= ", photo_path = :photo_path";
        }
        
        $sql .= " WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $params = [
                'id' => $id,
                'name' => $data['name'],
                'position' => $data['position'],
                'bio' => $data['bio'],
                'specialties' => $data['specialties'] ?? null,
                'qualifications' => $data['qualifications'] ?? null,
                'is_active' => $data['is_active'] ? 1 : 0,
                'display_order' => $data['display_order'] ?? 0
            ];

            if (isset($data['photo_path'])) {
                $params['photo_path'] = $data['photo_path'];
            }

            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error updating team member: " . $e->getMessage());
            return false;
        }
    }

    public function deleteMember($id) {
        $sql = "DELETE FROM team_members WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Error deleting team member: " . $e->getMessage());
            return false;
        }
    }
}
