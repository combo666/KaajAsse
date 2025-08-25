Security & SQL Inventory

This file lists locations in the codebase where raw SQL is used and should be migrated to prepared statements and reviewed for authorization checks and output escaping.

Files detected with `mysqli_query(`:

- src/board/kanban_board.php — multiple places (update, delete, select, leaderboard updates)
- src/team_members/team_members.php — several selects and queries
- src/projects/index.php — inserts/updates and selects
- src/leader_board/leaderboard.php — select
- src/projects/index.php — multiple queries
- src/board/kanban_board.php (again) — main Kanban endpoints
- index.php — some debug query usage

Recommended fix plan (priority order):
1. Convert authentication and registration (already done) to prepared statements (login/registration are updated).
2. Convert all `mysqli_query` calls that include user-supplied input to prepared statements.
3. Add output escaping (`htmlspecialchars`) when printing user or task fields.
4. Add CSRF protection for forms and AJAX endpoints.
5. Add authorization checks to mutation endpoints (ensure user owns task or has admin role).

Next actions I can take automatically if you approve:
- Migrate `src/board/kanban_board.php` endpoints to prepared statements and add basic role checks.
- Migrate `src/team_members/team_members.php` queries.
- Add a simple CSRF helper (generate/verify token) and wire it into forms and the Kanban AJAX endpoints.

Let me know which of these you'd like me to implement next; I can do them one or two files at a time and run quick smoke tests.
