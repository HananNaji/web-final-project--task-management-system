<?php
class User {

    private $name;
    private $address;
    private $dob;
    private $idNumber;
    private $email;
    private $telephone;
    private $role;
    private $qualification;
    private $skills;
    private $username;
    private $password;
    private $pdo; 

    public function __construct($pdo) {
        $this->pdo = $pdo; // تمرير الاتصال بقاعدة البيانات
    }

    // **دالة لحفظ البيانات في الخصائص**
    public function setUserData($data) {
        $this->name = $data['name'];
        $this->address = $data['address'];
        $this->dob = $data['dob'];
        $this->idNumber = $data['id_number'];
        $this->email = $data['email'];
        $this->telephone = $data['telephone'];
        $this->role = $data['role'];
        $this->qualification = $data['qualification'];
        $this->skills = $data['skills'];
        $this->username = $data['username'];
        $this->password = $data['password']; // يمكن تشفير كلمة المرور هنا
    }

    // **دالة لحفظ بيانات الخطوة في الجلسة**
    public function saveStep($step, $data) {
        $_SESSION[$step] = $data;
    }

    // **دمج بيانات الخطوات**
    public function mergeSteps() {
        if (isset($_SESSION['step1']) && isset($_SESSION['step2'])) {
            $mergedData = array_merge($_SESSION['step1'], $_SESSION['step2']);
            $this->setUserData($mergedData); // تخزين البيانات في الخصائص
            return $mergedData;
        }
        return null;
    }

    // **دالة للتحقق من وجود البيانات**
    public function validateSteps() {
        return isset($_SESSION['step1']) && isset($_SESSION['step2']);
    }
// **تسجيل المستخدم في قاعدة البيانات**
public function registerUser() {
    // التحقق من وجود اسم المستخدم أو البريد الإلكتروني مسبقًا
    $checkStmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
    $checkStmt->execute([
        'username' => $this->username,
        'email' => $this->email
    ]);
    $count = $checkStmt->fetchColumn();

    if ($count > 0) {
        throw new Exception("The username or email is already taken. Please choose another username or email.");
    }

    // إنشاء معرف المستخدم الفريد
    $user_id = str_pad(mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT);

    // إدخال المستخدم الجديد في قاعدة البيانات
    $stmt = $this->pdo->prepare("
        INSERT INTO users (name, address, dob, id_number, email, telephone, role, qualification, skills, username, password, user_id) 
        VALUES (:name, :address, :dob, :id_number, :email, :telephone, :role, :qualification, :skills, :username, :password, :user_id)
    ");
    $stmt->execute([
        'name' => $this->name,
        'address' => json_encode($this->address),
        'dob' => $this->dob,
        'id_number' => $this->idNumber,
        'email' => $this->email,
        'telephone' => $this->telephone,
        'role' => $this->role,
        'qualification' => $this->qualification,
        'skills' => $this->skills,
        'username' => $this->username,
        'password' => $this->password, // يتم تخزينها كما هي بدون تشفير
        'user_id' => $user_id
    ]);

    return $user_id;
}

}
?>
