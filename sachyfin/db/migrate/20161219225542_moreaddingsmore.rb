class Moreaddingsmore < ActiveRecord::Migration
  def change
    change_column :accounts, :last_transaction, :date
  end
end
