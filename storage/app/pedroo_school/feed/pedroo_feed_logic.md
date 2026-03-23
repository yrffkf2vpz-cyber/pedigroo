# Pedroo Feed Logic
Version: 1.0  
Author: Janos & Pedroo Core  
Scope: Defines how Pedroo generates, ranks, filters, and personalizes the social feed for each user.

---

## 1. Purpose of the Feed

The feed is the heart of the Pedigroo community.  
Pedroo uses it to:

- highlight important moments  
- promote community engagement  
- surface relevant content  
- support competitions  
- showcase achievements  
- connect users  

---

## 2. Feed Content Types

### 2.1 Timeline-Based Content
- milestones  
- event results  
- training progress  
- achievements  

### 2.2 Community Content
- photos  
- videos  
- posts  
- comments  
- trending breeds  

### 2.3 Competition Content
- weekly contest submissions  
- winners  
- virtual dog show highlights  

### 2.4 System Content
- badges  
- token rewards  
- subscription updates  

---

## 3. Feed Ranking Model

Each feed item receives a score:

| Factor | Weight | Description |
|--------|--------|-------------|
| Relevance | 40% | Breed, interests, dog age |
| Engagement | 30% | Likes, comments, views |
| Freshness | 20% | New or trending |
| Personalization | 10% | User history |

Scores are normalized to 0–1.

---

## 4. Feed Filtering Rules

Pedroo filters out:

- duplicates  
- spam  
- unsafe content  
- irrelevant breeds  
- low-quality posts  
- repeated system messages  

---

## 5. Personalization Rules

Pedroo personalizes based on:

### 5.1 User Behavior
- liked content  
- viewed content  
- followed dogs  
- followed kennels  

### 5.2 Dog Profile
- breed  
- age  
- activity level  
- training goals  

### 5.3 Engagement Patterns
- time-of-day activity  
- preferred content type  
- competition participation  

---

## 6. Competition Integration

The feed highlights:

- weekly contest submissions  
- winners  
- trending entries  
- breed-specific categories  
- virtual dog show results  

Pedroo boosts:
- new participants  
- underrepresented breeds  
- high-quality content  

---

## 7. Community Fairness Rules

Pedroo ensures:

- no breed bias  
- no popularity bias  
- no spam boosting  
- equal visibility for new users  
- fair competition exposure  

---

## 8. Feed Refresh Logic

### 8.1 Real-Time Refresh
Triggered by:
- new uploads  
- new events  
- new winners  

### 8.2 Scheduled Refresh
- hourly  
- daily  
- weekly  

### 8.3 Passive Refresh
Triggered by:
- user inactivity  
- low engagement  
- stale content  

---

## 9. Extensibility

Supports future modules:

- AI-based media scoring  
- kennel-level feed  
- breed spotlight weeks  
- sponsored content  
- advanced ranking models  

---

End of Document.