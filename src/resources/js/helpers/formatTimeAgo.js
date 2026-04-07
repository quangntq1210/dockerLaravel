const formatTimeAgo = (dateStr) => {
    const now = new Date();
    const date = new Date(dateStr);
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    if (diffMins < 1) return 'Vừa xong';
    if (diffMins < 60) return diffMins + ' phút trước';
    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) return diffHours + ' giờ trước';
    const diffDays = Math.floor(diffHours / 24);
    if (diffDays < 7) return diffDays + ' ngày trước';
    return date.toLocaleDateString('vi-VN');
}

export default formatTimeAgo;

window.formatTimeAgo = formatTimeAgo;