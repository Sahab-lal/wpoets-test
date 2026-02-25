# Answers to technical questions

1) How long did you spend on the coding test? What would you add to your solution if you had more time?

Time spent: I spent approximately 6–8 hours on the coding test. 

If I had more time I would:
- Add form validation feedback and inline previews for image/icon uploads.
- Add pagination, search, and sorting for the admin list.
- Add server-side validation and stricter file checks (size limits, MIME verification).
- Add tests for CRUD and upload logic.
- Add AJAX-based CRUD with real-time updates
- Improve accessibility and keyboard navigation for tabs/accordion/carousel.
- Optimize database queries and caching for performance
- Improve mobile UX and animations

2) How would you track down a performance issue in production? Have you ever had to do this?

I would start by confirming the symptoms (latency, error rate, CPU/memory spikes), then narrow it down:
- Check monitoring dashboards (APM traces, slow queries, error logs).
- Add or review tracing around slow endpoints to identify hotspots.
- Inspect DB queries and indexes, run EXPLAIN, and look for N+1 patterns.
- Profile server resources and check for rate spikes or large payloads.
- Reproduce in staging with similar data and load.

Yes, I have tracked down production performance issues by combining APM traces with DB query analysis and targeted logging to isolate slow endpoints.

3) Please describe yourself using JSON.

{
  "name": "Sahab Lal Gautam",
  "role": "Full-stack developer",
  "experience": "+4 years",
  "strengths": ["problem solving", "clean code", "UI details", "debugging"],
  "focus": ["PHP", "MySQL", "JavaScript", "Bootstrap", "CodeIgniter", "AJAX"],
  "values": ["clarity", "reliability", "learning"],
  "availability": "open to discuss"
}
