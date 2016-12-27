class Changelenghts < ActiveRecord::Migration
  def change
    change_column :transactions, :description, :text
    change_column :transactions, :amount, :decimal, precision: 2
    change_column :transactions, :start_saldo, :decimal, precision: 2
    change_column :transactions, :end_saldo, :decimal, precision: 2
  end
end
