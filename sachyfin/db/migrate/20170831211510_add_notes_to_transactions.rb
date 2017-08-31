class AddNotesToTransactions < ActiveRecord::Migration
  def change
    add_column :transactions, :note, :string
  end
end
