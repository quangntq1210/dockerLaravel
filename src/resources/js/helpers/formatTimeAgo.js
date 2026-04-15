const t = window.timeAgoI18n || {};

const withCount = (template, count) =>
    String(template || '').replace(':count', count);

const formatTimeAgo = (dateStr) => {
    const now = new Date();
    const date = new Date(dateStr);
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);

    if (diffMins < 1) return t.just_now || 'Just now';
    if (diffMins < 60) return withCount(t.minutes_ago || ':count minutes ago', diffMins);

    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) return withCount(t.hours_ago || ':count hours ago', diffHours);

    const diffDays = Math.floor(diffHours / 24);
    if (diffDays < 7) return withCount(t.days_ago || ':count days ago', diffDays);

    return date.toLocaleDateString(t.locale === 'vi' ? 'vi-VN' : 'en-US');
};

export default formatTimeAgo;
window.formatTimeAgo = formatTimeAgo;