--
-- Ajout / mise à jour d'un flag pour l'état de la base
-- (afin d'opérer les actions utiles suite à un import)
--

DO $$
  BEGIN
    IF EXISTS (
      SELECT 1
      FROM   information_schema.tables
      WHERE  table_schema = 'public'
      AND    table_name = 'variable'
    )
    THEN
      DELETE FROM variable WHERE name = 'tic_db_status'
    ;
    END IF ;
  END
$$ ;
