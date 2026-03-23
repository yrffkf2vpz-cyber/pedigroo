# Pedroo Timeline Rules
Version: 1.0  
Author: Janos & Pedroo Core  
Scope: Defines how Pedroo generates, structures, and maintains dog timelines based on events, media, and user activity.

---

## 1. Purpose of the Timeline

The timeline is the dog’s life story.  
Pedroo uses it to:

- document milestones  
- record events  
- track development  
- highlight achievements  
- support recommendations  
- feed the social system  

Every timeline entry must be meaningful, structured, and breed-aware.

---

## 2. Timeline Entry Types

### 2.1 Automatic Entries
- event results  
- weekly contest participation  
- virtual dog show submissions  
- token rewards  
- training milestones  
- health updates (non-diagnostic)  

### 2.2 Manual Entries
- photos  
- videos  
- notes  
- achievements  
- daily moments  

### 2.3 System Entries
- profile completion  
- subscription changes  
- badges earned  
- ranking updates  

---

## 3. Timeline Entry Structure

Each entry contains:

- **entry_id**  
- **dog_id**  
- **type**  
- **category**  
- **timestamp**  
- **media (optional)**  
- **description**  
- **tags**  
- **visibility**  
- **source (user/system/Pedroo)**  

---

## 4. Timeline Generation Rules

### 4.1 Event-Based Entries
When an event occurs:
- Pedroo creates a timeline entry  
- normalizes the result  
- adds breed-specific tags  
- links to competition details  

### 4.2 Media-Based Entries
When a user uploads:
- Pedroo detects type (photo/video)  
- suggests timeline placement  
- auto-tags breed, age, context  

### 4.3 Weekly Contest Entries
Pedroo automatically:
- adds submission entries  
- adds winner entries  
- adds reward entries  

### 4.4 Development Entries
Triggered by:
- age milestones  
- training progress  
- activity streaks  

---

## 5. Tagging Rules

Pedroo tags entries with:

### 5.1 Breed Tags
- breed name  
- breed group  
- breed-specific traits  

### 5.2 Activity Tags
- training  
- grooming  
- play  
- sport  
- show  

### 5.3 Event Tags
- competition type  
- placement  
- ranking  

### 5.4 System Tags
- subscription  
- token  
- badge  

---

## 6. Timeline Ordering Rules

- Entries are sorted by timestamp  
- System entries are grouped  
- Milestones are highlighted  
- Duplicate entries are merged  
- Low-value entries are minimized  

---

## 7. Timeline Quality Rules

Pedroo ensures:

- no spam  
- no duplicates  
- no irrelevant entries  
- no empty entries  
- no unsafe content  

---

## 8. Extensibility

Supports future modules:

- AI-based media classification  
- automatic milestone detection  
- breed-specific timeline templates  
- kennel-level timelines  

---

End of Document.