export function axiosErrorHandler(status_code, error_messages) {
  let alert_message = '';
  switch (status_code) {
    // validation error(resources.lang.ja.validation.php)\
    case 403:
      alert_message += error_messages;
      break;
    case 409:
      alert_message += error_messages;
      break;
    case 422:
      if (error_messages.email) {
        alert_message += error_messages.email + "\n";
      }
      if (error_messages.auth_id) {
        alert_message += error_messages.auth_id + "\n";
      }
      if (error_messages.password) {
        alert_message += error_messages.password;
      }
      if (error_messages.old_password) {
        alert_message += error_messages.old_password;
      }
      if (error_messages.new_password) {
        alert_message += error_messages.new_password;
      }
      if (error_messages.confirm_password) {
        alert_message += error_messages.confirm_password;
      }
      if (error_messages.auth_code) {
        alert_message += error_messages.auth_code;
      }
      if (error_messages.name) {
        alert_message += error_messages.name;
      }
      if (error_messages.birth_day) {
        alert_message += error_messages.birth_day;
      }
      if (error_messages.exercise_details) {
        alert_message += error_messages.exercise_details;
      }
      if (error_messages.date) {
        alert_message += error_messages.date;
      }
      if (error_messages.week_day_id) {
        alert_message += error_messages.week_day_id;
      }
      if (error_messages.body_part_id) {
        alert_message += error_messages.body_part_id;
      }
      if (error_messages.exercise_type_name) {
        alert_message += error_messages.exercise_type_name;
      }
      break;
    // HTTPステータスに応じて処理
    case 423:
      alert_message += error_messages;
      break;
    default:
      break;
    // 例外処理
  }
  return alert_message
}
