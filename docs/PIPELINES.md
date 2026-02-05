# MongoDB Pipelines in BloodBankDonor

This document lists all **aggregation pipelines** and **update pipelines** used in the project, where they are used, and why.

---

## 1. Aggregation pipeline (read / analytics)

### Command

**MongoDB command:** `aggregate`  
**Collection method:** `$collection->aggregate($pipeline)`

### Where used

| File | Line(s) | Collection |
|------|---------|------------|
| `api/get_dashboard_stats.php` | 4–9 | `donors` |

### Pipeline definition

```php
$pipeline = [
    ['$match'  => ['available' => true]],
    ['$group'  => ['_id' => '$blood_group', 'count' => ['$sum' => 1]]]
];
$cursor = $donorsCollection->aggregate($pipeline);
```

### Stage-by-stage

| Stage | Operator | Purpose |
|-------|----------|---------|
| 1 | `$match` | Keep only donors where `available === true`. |
| 2 | `$group` | Group by `blood_group`, count documents per group; output `_id` = blood group, `count` = number. |

### Why used

- To get **per–blood-group counts of available donors** for dashboard or stats.
- `$match` restricts to available donors; `$group` aggregates by `blood_group` and sums 1 per document.

### Raw MongoDB equivalent

```javascript
db.donors.aggregate([
  { $match: { available: true } },
  { $group: { _id: "$blood_group", count: { $sum: 1 } } }
])
```

### Output shape

Each cursor document has:

- `_id`: blood group (e.g. `"A+"`, `"O-"`).
- `count`: number of available donors for that group.

The script then maps this to `["blood_group" => $d["_id"], "count" => $d["count"]]` and returns JSON.

---

## 2. Update pipeline (write)

### Command

**MongoDB command:** `update` (with pipeline-style update)  
**Collection method:** `$collection->updateOne($filter, $update)` where `$update` is an **array of stages** (pipeline).

### Where used

| File | Line(s) | Collection |
|------|---------|------------|
| `api/admin/update_request.php` | 46–55 | `blood_requests` |

### Pipeline definition

```php
$update = [
    [ '$set' => [ 'status' => $status, 'updatedAt' => date('c') ] ]
];
$result = $requestsCollection->updateOne(["id" => $idStr], $update);
```

(There is a fallback that uses the same `$update` with filter `["_id" => new ObjectId($idStr)]` for older documents.)

### Stage-by-stage

| Stage | Operator | Purpose |
|-------|----------|---------|
| 1 | `$set` | Set `status` (e.g. `"accepted"` / `"rejected"`) and `updatedAt` (current time in ISO 8601). |

### Why used

- To change **blood request status** (Accept/Reject) and **updatedAt** in one update.
- An **update pipeline** (array of stages) is used instead of a plain `{ $set: ... }` document because the PHP MongoDB library (`mongodb/mongodb`) was rejecting the single-document update in this context; the pipeline form is accepted and behaves the same for this use case.
- Uses string `id` first; falls back to `_id` (ObjectId) for older documents.

### Raw MongoDB equivalent

```javascript
db.blood_requests.updateOne(
  { id: "<request_id_string>" },
  [ { $set: { status: "accepted", updatedAt: new Date().toISOString() } } ]
)
```

### Result

- One document is updated (by `id` or `_id`).
- `status` and `updatedAt` are set; other fields unchanged.

---

## 3. Other update usage (not pipelines)

These use **update documents** with `$set`, not aggregation/update pipelines, but are listed for completeness.

| File | Method | Update type | Purpose |
|------|--------|-------------|---------|
| `api/save_donor.php` | `updateOne` | Document: `['$set' => [...]]` | Sync donor profile from Firebase to MongoDB (upsert by `uid`). |
| `api/admin/update_donor.php` | `updateOne` | Document: `['$set' => [...]]` | Admin edits donor (name, blood_group, city, mobile, available) by `_id`. |

---

## Summary

| Type | Where | Pipeline / update | Why |
|------|--------|-------------------|-----|
| **Aggregation** | `api/get_dashboard_stats.php` | `$match` → `$group` | Count available donors per blood group. |
| **Update pipeline** | `api/admin/update_request.php` | `[ { $set: { status, updatedAt } } ]` | Accept/Reject blood request and set timestamp; works with PHP driver. |
