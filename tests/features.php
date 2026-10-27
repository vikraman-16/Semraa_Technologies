<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(403);exit;}
putenv('APP_URL=http://127.0.0.1:8091');putenv('APP_ENV=local');
require_once __DIR__.'/../includes/features.php';
function test_schema(PDO $pdo): void {
 $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);$pdo->exec('PRAGMA foreign_keys=ON');
 $pdo->exec("CREATE TABLE contact_requests (id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT,email TEXT,phone TEXT DEFAULT '',subject TEXT,message TEXT,status TEXT DEFAULT 'new',internal_email_status TEXT DEFAULT 'pending',created_at TEXT DEFAULT CURRENT_TIMESTAMP);
 CREATE TABLE demo_requests (id INTEGER PRIMARY KEY AUTOINCREMENT,service_slug TEXT,name TEXT,email TEXT,phone TEXT DEFAULT '',company TEXT DEFAULT '',job_title TEXT DEFAULT '',preferred_date TEXT,preferred_time TEXT,timezone TEXT DEFAULT 'Asia/Kolkata',mode TEXT,extra_data TEXT DEFAULT '{}',message TEXT DEFAULT '',status TEXT DEFAULT 'new',internal_email_status TEXT DEFAULT 'pending',confirmation_email_status TEXT DEFAULT 'pending',created_at TEXT DEFAULT CURRENT_TIMESTAMP);
 CREATE TABLE enquiry_notes (id INTEGER PRIMARY KEY AUTOINCREMENT,request_type TEXT,request_id INTEGER,note TEXT,created_at TEXT DEFAULT CURRENT_TIMESTAMP);
 CREATE TABLE enquiry_briefs (contact_id INTEGER PRIMARY KEY REFERENCES contact_requests(id),brief_json TEXT);
 CREATE TABLE consultation_slots (id INTEGER PRIMARY KEY AUTOINCREMENT,starts_at TEXT UNIQUE,status TEXT DEFAULT 'available',created_at TEXT DEFAULT CURRENT_TIMESTAMP);
 CREATE TABLE consultation_bookings (id INTEGER PRIMARY KEY AUTOINCREMENT,slot_id INTEGER UNIQUE REFERENCES consultation_slots(id),name TEXT,email TEXT,phone TEXT DEFAULT '',message TEXT,status TEXT DEFAULT 'confirmed',internal_email_status TEXT DEFAULT 'pending',confirmation_email_status TEXT DEFAULT 'pending',created_at TEXT DEFAULT CURRENT_TIMESTAMP);
 CREATE TABLE case_studies (id INTEGER PRIMARY KEY AUTOINCREMENT,slug TEXT UNIQUE,title TEXT,summary TEXT,client_name TEXT DEFAULT '',challenge TEXT,solution TEXT,outcome TEXT,project_url TEXT DEFAULT '',image_path TEXT DEFAULT '',image_alt TEXT DEFAULT '',status TEXT DEFAULT 'draft',evidence_approved INTEGER DEFAULT 0,created_at TEXT DEFAULT CURRENT_TIMESTAMP,updated_at TEXT DEFAULT CURRENT_TIMESTAMP);");
}
if(($argv[1]??'')==='fixture'){
 $path=dirname(__DIR__).'/.runtime/features-'.bin2hex(random_bytes(5)).'.sqlite';$pdo=new PDO('sqlite:'.$path);test_schema($pdo);echo $path;exit;
}
$pdo=new PDO('sqlite::memory:');test_schema($pdo);$checks=0;
function check_feature(bool $passed,string $label): void {global $checks;if(!$passed)throw new RuntimeException('FAIL: '.$label);$checks++;}
[$brief,$errors]=validate_brief(['brief_goal'=>'Custom business software','brief_timeline'=>'Flexible','brief_budget'=>'Need guidance']);check_feature(!$errors&&count($brief)===3,'Optional brief validates');
[, $errors]=validate_brief(['brief_goal'=>'malicious option']);check_feature(isset($errors['brief_goal']),'Unknown brief options rejected');
[$briefEmpty,$errors]=validate_brief([]);check_feature(!$errors&&$briefEmpty===[],'Brief is optional');
$contact=['name'=>'Test User','email'=>'test@example.org','phone'=>'','subject'=>'Software enquiry','message'=>'A test message'];
$id=save_contact($pdo,$contact,$brief);check_feature($id===1,'Contact saved');check_feature((int)$pdo->query('SELECT COUNT(*) FROM enquiry_briefs')->fetchColumn()===1,'Brief stored with contact');
check_feature(json_decode($pdo->query('SELECT brief_json FROM enquiry_briefs')->fetchColumn(),true)===$brief,'Brief values preserved');
$local=(new DateTimeImmutable('+2 days',new DateTimeZone(config('timezone'))))->setTime(10,0)->format('Y-m-d\TH:i');publish_slot($pdo,$local);check_feature(count(available_slots($pdo))===1,'Published slot becomes available');
try{publish_slot($pdo,$local);check_feature(false,'Duplicate time rejected');}catch(PDOException $e){check_feature(true,'Duplicate time rejected');}
try{publish_slot($pdo,'2020-01-01T10:00');check_feature(false,'Past slot rejected');}catch(InvalidArgumentException $e){check_feature(true,'Past slot rejected');}
$bookingValues=['slot_id'=>1,'name'=>'Test User','email'=>'test@example.org','phone'=>'','message'=>'Test only'];
$booking=book_consultation($pdo,$bookingValues);check_feature($booking['id']===1,'Booking saved');check_feature(count(available_slots($pdo))===0,'Booked slot removed from availability');
try{book_consultation($pdo,$bookingValues);check_feature(false,'Double booking prevented');}catch(InvalidArgumentException $e){check_feature(true,'Double booking prevented');}
check_feature((int)$pdo->query('SELECT COUNT(*) FROM consultation_bookings')->fetchColumn()===1,'Only one booking exists');
$utc=gmdate('Y-m-d H:i:s',time()+2*86400);$pdo->prepare('INSERT INTO consultation_slots (starts_at) VALUES (?)')->execute([$utc]);
$pdo->exec("CREATE TRIGGER fail_booking BEFORE INSERT ON consultation_bookings BEGIN SELECT RAISE(ABORT,'test failure'); END;");
try{book_consultation($pdo,array_replace($bookingValues,['slot_id'=>2]));}catch(PDOException $e){}
check_feature($pdo->query('SELECT status FROM consultation_slots WHERE id=2')->fetchColumn()==='available','Booking failure rolls back slot claim');
[$values,$errors]=validate_consultation(['slot_id'=>'1','name'=>'User','email'=>'invalid']);check_feature(isset($errors['email']),'Consultation email validated');
$case=['slug'=>'test-only','title'=>'Test only','status'=>'draft'];[$values,$errors]=validate_case($case);check_feature(!$errors,'Incomplete draft permitted');
[$values,$errors]=validate_case(array_replace($case,['status'=>'published']));check_feature(isset($errors['evidence_approved'],$errors['challenge']),'Publishing requires real approved content');
$pdo->exec("INSERT INTO case_studies (slug,title,summary,challenge,solution,outcome,status,evidence_approved) VALUES ('draft','Test draft','','','','','draft',0)");check_feature(case_records($pdo)===[],'Draft not public');
$pdo->exec("UPDATE case_studies SET status='published' WHERE slug='draft'");check_feature(case_records($pdo)===[],'Unapproved record not public');
$pdo->exec("UPDATE case_studies SET evidence_approved=1 WHERE slug='draft'");check_feature(count(case_records($pdo))===1,'Published approved record available');
echo "PASS: $checks feature integration checks using isolated SQLite storage; no email sent.\n";
