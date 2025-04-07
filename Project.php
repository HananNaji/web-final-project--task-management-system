<?php
class Project {
    private $projectId;
    private $projectTitle;
    private $projectDescription;
    private $customerName;
    private $totalBudget;
    private $startDate;
    private $endDate;
    private $documents = []; 
    private $documentsTitle;

    private $pdo; 
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function setProjectData($data) {
        $this->projectId = $data['project_id'];
        $this->projectTitle = $data['project_title'];
        $this->projectDescription = $data['project_description'];
        $this->customerName = $data['customer_name'];
        $this->totalBudget = $data['total_budget'];
        $this->startDate = $data['start_date'];
        $this->endDate = $data['end_date'];
        $this->documentsTitle = $data['documents_title'];

    }

    public function addDocuments($files) {
        foreach ($files['tmp_name'] as $key => $tmp_name) {
            $fileName = $files['name'][$key];
            $fileSize = $files['size'][$key];
            $fileTmp = $files['tmp_name'][$key];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($fileExt, ['pdf', 'docx', 'png', 'jpg'])) {
                throw new Exception("Invalid file type: $fileExt");
            }

            if ($fileSize > 2 * 1024 * 1024) {
                throw new Exception("File size exceeds 2MB for $fileName");
            }

            $filePath = "images/" . uniqid() . "_" . $fileName;
            move_uploaded_file($fileTmp, $filePath);
            $this->documents[] = $filePath; 
        }
    }

    public function validateProject() {
        if (!preg_match('/^[A-Z]{4}-\d{5}$/', $this->projectId)) {
            throw new Exception("Invalid Project ID format.");
        }

        if ($this->endDate <= $this->startDate) {
            throw new Exception("End date must be later than start date.");
        }

        if ($this->totalBudget <= 0) {
            throw new Exception("Budget must be a positive number.");
        }
    }

    public function saveProject() {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO projects (
                    project_id, 
                    project_title, 
                    project_description, 
                    customer_name, 
                    total_budget, 
                    start_date, 
                    end_date, 
                    documents_title, 
                    supporting_documents
                ) VALUES (
                    :project_id, 
                    :project_title, 
                    :project_description, 
                    :customer_name, 
                    :total_budget, 
                    :start_date, 
                    :end_date, 
                    :documents_title, 
                    :supporting_documents
                )
            ");
    
            $stmt->execute([
                'project_id' => $this->projectId,
                'project_title' => $this->projectTitle,
                'project_description' => $this->projectDescription,
                'customer_name' => $this->customerName,
                'total_budget' => $this->totalBudget,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'documents_title' => $this->documentsTitle,
                'supporting_documents' => json_encode($this->documents)
            ]);
    
            foreach ($this->documents as $filePath) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO project_files (project_id, file_path, file_title) 
                    VALUES (:project_id, :file_path, :file_title)
                ");
                $stmt->execute([
                    'project_id' => $this->projectId,
                    'file_path' => $filePath,
                    'file_title' => $this->documentsTitle 
                ]);
            }
        } catch (PDOException $e) {
            throw new Exception("Error saving project: " . $e->getMessage());
        }
    }
    

    public function fetchProject($projectId) {
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE project_id = :project_id");
        $stmt->execute(['project_id' => $projectId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
