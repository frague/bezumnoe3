DELIMITER |



CREATE FUNCTION WEIGHTED_AVERAGE (
      ForumId INT, 
      Type TINYINT,
      UserId BIGINT,
      Title TEXT,
      Content TEXT,
      Address TEXT,
      Guid TEXT,
      IsCommentable TINYINT,
      ThreadId BIGINT,
      ThreadOrder INT,
      Depth INT,
      VisibleTo BIGINT)
  RETURNS BIGINT
   BEGIN

    INSERT INTO forum_records (
      FORUM_ID,
      TYPE,
      USER_ID,
      TITLE,
      CONTENT,
      DATE,
      ADDRESS,
      CLICKS,
      GUID,
      IS_COMMENTABLE,
      UPDATE_DATE,
      THREAD_ID,
      THREAD_ORDER,
      DEPTH,
      VISIBLE_TO
  ) VALUES (
      ForumId, 
      Type,
      UserId,
      Title,
      Content,
      NOW(),
      Address,
      0,
      Guid,
      IsCommentable,
      NOW(),
      ThreadId,
      ThreadOrder,
      Depth,
      VisibleTo
  );

   RETURN LAST_INSERT_ID();
END|
